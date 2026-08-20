import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { RolUsuario } from '../../../core/models/usuario.model';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent {
  private readonly fb = inject(FormBuilder);
  private readonly auth = inject(AuthService);
  private readonly router = inject(Router);

  readonly cargando = signal(false);
  readonly error = signal<string | null>(null);
  readonly info = signal<string | null>(null);

  // 'credenciales' = correo/contraseña · 'codigo' = verificación 2FA
  readonly paso = signal<'credenciales' | 'codigo'>('credenciales');
  readonly correoPendiente = signal<string>('');

  form = this.fb.group({
    correo: ['', [Validators.required, Validators.email]],
    password: ['', [Validators.required, Validators.minLength(6)]]
  });

  formCodigo = this.fb.group({
    codigo: ['', [Validators.required, Validators.pattern(/^\d{4}$/)]]
  });

  /** Paso 1: envía credenciales. */
  enviar(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.cargando.set(true);
    this.error.set(null);
    this.info.set(null);

    const { correo, password } = this.form.getRawValue();

    this.auth.login({ correo: correo!, password: password! }).subscribe({
      next: (res) => {
        this.cargando.set(false);
        if (res.tipo === 'verificacion') {
          this.correoPendiente.set(res.correo);
          this.paso.set('codigo');
          this.info.set('Te enviamos un código de 4 dígitos a ' + res.correo + '.');
        } else {
          this.router.navigate([this.rutaSegunRol(res.usuario.rol)]);
        }
      },
      error: (err) => {
        this.cargando.set(false);
        this.error.set(this.mensajeError(err, 'No se pudo iniciar sesión. Verifica tus datos.'));
      }
    });
  }

  /** Paso 2: verifica el código recibido por correo. */
  verificar(): void {
    if (this.formCodigo.invalid) {
      this.formCodigo.markAllAsTouched();
      return;
    }

    this.cargando.set(true);
    this.error.set(null);

    const codigo = this.formCodigo.getRawValue().codigo!;

    this.auth.verificarCodigo(this.correoPendiente(), codigo).subscribe({
      next: (usuario) => {
        this.cargando.set(false);
        this.router.navigate([this.rutaSegunRol(usuario.rol)]);
      },
      error: (err) => {
        this.cargando.set(false);
        this.error.set(this.mensajeError(err, 'Código incorrecto. Intenta de nuevo.'));
      }
    });
  }

  reenviar(): void {
    this.error.set(null);
    this.info.set(null);
    this.auth.reenviarCodigo(this.correoPendiente()).subscribe({
      next: (r) => this.info.set(r.mensaje),
      error: (err) => this.error.set(this.mensajeError(err, 'No se pudo reenviar el código.'))
    });
  }

  volver(): void {
    this.paso.set('credenciales');
    this.error.set(null);
    this.info.set(null);
    this.formCodigo.reset();
  }

  private rutaSegunRol(rol: RolUsuario): string {
    const mapa: Record<RolUsuario, string> = {
      administrador: '/panel/administrador',
      instructor: '/panel/instructor',
      estudiante: '/panel/estudiante'
    };
    return mapa[rol];
  }

  private mensajeError(err: unknown, porDefecto: string): string {
    const e = err as { error?: { mensaje?: string }; mensaje?: string; message?: string };
    return e?.error?.mensaje ?? e?.mensaje ?? e?.message ?? porDefecto;
  }
}
