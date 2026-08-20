/**
 * El backend real (PHP, carpeta CTROL-Z) guarda el rol como `tipoper`
 * numérico en la tabla `persona`: 1 Administrador, 2 Docente, 3 Estudiante.
 * En el frontend usamos un tipo legible; `tipoperARol` hace la conversión.
 */
export type RolUsuario = 'administrador' | 'instructor' | 'estudiante';

export const TIPOPER_A_ROL: Record<number, RolUsuario> = {
  1: 'administrador',
  2: 'instructor',
  3: 'estudiante'
};

export const ROL_A_TIPOPER: Record<RolUsuario, number> = {
  administrador: 1,
  instructor: 2,
  estudiante: 3
};

export function tipoperARol(tipoper: number): RolUsuario | null {
  return TIPOPER_A_ROL[tipoper] ?? null;
}

/** Forma en la que el backend PHP puede devolver al usuario. */
export interface UsuarioBackend {
  id: number;
  nombres: string;
  apellidos: string;
  correo: string;
  tipoper: number;
  estado?: number;
}

export interface Usuario {
  id: number;
  nombres: string;
  apellidos: string;
  correo: string;
  rol: RolUsuario;
  tipoper: number;
}

export function usuarioDesdeBackend(u: UsuarioBackend): Usuario {
  return {
    id: u.id,
    nombres: u.nombres,
    apellidos: u.apellidos,
    correo: u.correo,
    tipoper: u.tipoper,
    rol: tipoperARol(u.tipoper) ?? 'estudiante'
  };
}

/**
 * El backend devuelve, tras verificar el código, un `redirect` del tipo
 * 'admin/index.php' | 'docente/index.php' | 'estudiante/index.php'.
 * Con eso armamos el usuario (rol y nombre) para el frontend.
 */
export function usuarioDesdeRedirect(redirect: string, mensaje?: string | null): Usuario {
  const clave = (redirect || '').split('/')[0];
  const mapa: Record<string, { rol: RolUsuario; tipoper: number }> = {
    admin: { rol: 'administrador', tipoper: 1 },
    docente: { rol: 'instructor', tipoper: 2 },
    estudiante: { rol: 'estudiante', tipoper: 3 }
  };
  const m = mapa[clave];
  if (!m) {
    throw new Error('Tu cuenta no tiene un tipo de acceso válido asignado.');
  }
  const nombres = (mensaje || '')
    .replace(/^Bienvenido\/a,\s*/i, '')
    .replace(/\.$/, '')
    .trim() || 'Usuario';

  return { id: 0, nombres, apellidos: '', correo: '', rol: m.rol, tipoper: m.tipoper };
}

export interface RegistroRequest {
  nombres: string;
  apellidos: string;
  telefono: string;
  correo: string;
  password: string;
  ci: string;
  extension?: string;
  f_nac: string;
  sexo: 'M' | 'F';
  estcivil: 'soltero' | 'casado';
  rol: RolUsuario;
}

export interface LoginRequest {
  correo: string;
  password: string;
}

/**
 * Resultado del primer paso del login:
 * - 'verificacion' -> el backend mandó un código al correo (2FA).
 * - 'autenticado'  -> (compatibilidad) el backend devolvió el usuario directo.
 */
export type LoginResultado =
  | { tipo: 'verificacion'; correo: string }
  | { tipo: 'autenticado'; usuario: Usuario };

/** Respuesta cruda de AuthController.php (login / verificar-codigo / me). */
export interface AuthApiResponse {
  ok: boolean;
  mensaje: string | null;
  data:
    | {
        redirect?: string;
        usuario?: UsuarioBackend;
        requiere_verificacion?: boolean;
        correo?: string;
      }
    | null;
}
