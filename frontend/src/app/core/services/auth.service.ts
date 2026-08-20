import { Injectable, computed, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, catchError, map, of, tap } from 'rxjs';
import {
  AuthApiResponse,
  LoginRequest,
  LoginResultado,
  RegistroRequest,
  Usuario,
  usuarioDesdeBackend,
  usuarioDesdeRedirect
} from '../models/usuario.model';
import { comoFormUrlEncoded, HEADERS_FORM_URLENCODED, urlControlador } from './php-api.util';

const USUARIO_KEY = 'ctrolz_usuario';

/**
 * Autenticación contra el backend PHP real (sesión de servidor vía
 * cookie, no JWT). El login tiene verificación en dos pasos (2FA):
 *   1) login(correo, password)  -> el backend manda un código al correo.
 *   2) verificarCodigo(correo, codigo) -> abre la sesión y entra.
 */
@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private readonly usuarioActual = signal<Usuario | null>(this.leerUsuarioGuardado());
  private readonly sesionVerificada = signal(false);

  readonly usuario = computed(() => this.usuarioActual());
  readonly estaAutenticado = computed(() => !!this.usuarioActual());
  readonly rol = computed(() => this.usuarioActual()?.rol ?? null);
  readonly listoParaGuards = computed(() => this.sesionVerificada());

  constructor(private http: HttpClient) {}

  /**
   * Paso 1 del login. Devuelve si hace falta verificar por código (2FA)
   * o —por compatibilidad— el usuario ya autenticado.
   */
  login(datos: LoginRequest): Observable<LoginResultado> {
    const body = comoFormUrlEncoded({ correo: datos.correo, password: datos.password });

    return this.http
      .post<AuthApiResponse>(urlControlador('AuthController', 'login'), body, {
        headers: HEADERS_FORM_URLENCODED,
        withCredentials: true
      })
      .pipe(
        map((respuesta): LoginResultado => {
          if (!respuesta.ok || !respuesta.data) {
            throw respuesta;
          }
          // Camino con 2FA: el backend pide el código de verificación.
          if (respuesta.data.requiere_verificacion) {
            return { tipo: 'verificacion', correo: respuesta.data.correo ?? datos.correo };
          }
          // Camino directo (por si el backend devuelve el usuario).
          if (respuesta.data.usuario) {
            const usuario = usuarioDesdeBackend(respuesta.data.usuario);
            this.guardarUsuario(usuario);
            return { tipo: 'autenticado', usuario };
          }
          throw new Error('Respuesta de login inesperada.');
        })
      );
  }

  /** Paso 2 del login: valida el código de 4 dígitos y abre la sesión. */
  verificarCodigo(correo: string, codigo: string): Observable<Usuario> {
    const body = comoFormUrlEncoded({ correo, codigo });

    return this.http
      .post<AuthApiResponse>(urlControlador('AuthController', 'verificar-codigo'), body, {
        headers: HEADERS_FORM_URLENCODED,
        withCredentials: true
      })
      .pipe(
        map((respuesta) => {
          if (!respuesta.ok || !respuesta.data) {
            throw respuesta;
          }
          const usuario = respuesta.data.usuario
            ? usuarioDesdeBackend(respuesta.data.usuario)
            : usuarioDesdeRedirect(respuesta.data.redirect ?? '', respuesta.mensaje);
          // Guardamos el correo real (el redirect no lo trae).
          usuario.correo = usuario.correo || correo;
          this.guardarUsuario(usuario);
          return usuario;
        })
      );
  }

  /** Registro público (crea persona + usuario en el backend PHP). */
  registrar(datos: RegistroRequest): Observable<{ mensaje: string }> {
    const body = comoFormUrlEncoded({
      nombres: datos.nombres,
      apellidos: datos.apellidos,
      telefono: datos.telefono,
      correo: datos.correo,
      password: datos.password,
      ci: datos.ci,
      extension: datos.extension ?? '',
      f_nac: datos.f_nac,
      sexo: datos.sexo,
      estcivil: datos.estcivil,
      tipoper: rolATipoperPublico(datos.rol)
    });

    return this.http
      .post<AuthApiResponse>(urlControlador('PersonaController', 'registrar'), body, {
        headers: HEADERS_FORM_URLENCODED,
        withCredentials: true
      })
      .pipe(
        map((respuesta) => {
          if (!respuesta.ok) {
            throw respuesta;
          }
          return { mensaje: respuesta.mensaje ?? 'Registro realizado correctamente.' };
        })
      );
  }

  /** Reenvía un nuevo código al correo pendiente de verificación. */
  reenviarCodigo(correo: string): Observable<{ mensaje: string }> {
    const body = comoFormUrlEncoded({ correo });

    return this.http
      .post<AuthApiResponse>(urlControlador('AuthController', 'reenviar-codigo'), body, {
        headers: HEADERS_FORM_URLENCODED,
        withCredentials: true
      })
      .pipe(
        map((respuesta) => {
          if (!respuesta.ok) {
            throw respuesta;
          }
          return { mensaje: respuesta.mensaje ?? 'Te enviamos un nuevo código.' };
        })
      );
  }

  /**
   * Al arrancar la app intentamos restaurar la sesión. Este backend no
   * tiene endpoint `me`, así que si la llamada falla NO borramos al
   * usuario guardado en localStorage: confiamos en esa copia local para
   * mantener la navegación tras un F5. La seguridad real la aplica cada
   * endpoint del backend por su cuenta.
   */
  restaurarSesion(): Observable<Usuario | null> {
    return this.http
      .get<AuthApiResponse>(urlControlador('AuthController', 'me'), { withCredentials: true })
      .pipe(
        map((respuesta) => {
          if (respuesta.ok && respuesta.data?.usuario) {
            const usuario = usuarioDesdeBackend(respuesta.data.usuario);
            this.guardarUsuario(usuario);
            return usuario;
          }
          return this.usuarioActual();
        }),
        catchError(() => of(this.usuarioActual())),
        tap(() => this.sesionVerificada.set(true))
      );
  }

  logout(): void {
    this.http
      .get(urlControlador('AuthController', 'logout') + '&json=1', { withCredentials: true })
      .subscribe({
        next: () => this.limpiarUsuario(),
        error: () => this.limpiarUsuario()
      });
    // Limpiamos ya el estado local sin esperar la respuesta del servidor.
    this.limpiarUsuario();
  }

  private guardarUsuario(usuario: Usuario): void {
    localStorage.setItem(USUARIO_KEY, JSON.stringify(usuario));
    this.usuarioActual.set(usuario);
  }

  private limpiarUsuario(): void {
    localStorage.removeItem(USUARIO_KEY);
    this.usuarioActual.set(null);
  }

  private leerUsuarioGuardado(): Usuario | null {
    const crudo = localStorage.getItem(USUARIO_KEY);
    return crudo ? (JSON.parse(crudo) as Usuario) : null;
  }
}

/** El registro público solo permite instructor (2) o estudiante (3). */
function rolATipoperPublico(rol: RegistroRequest['rol']): number {
  return rol === 'instructor' ? 2 : 3;
}
