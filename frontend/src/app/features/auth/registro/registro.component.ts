import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { RolUsuario } from '../../../core/models/usuario.model';

@Component({
  selector: 'app-registro',
  standalone: true,
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './registro.component.html',
  styleUrl: './registro.component.css'
})
export class RegistroComponent {
  private readonly fb = inject(FormBuilder);
  private readonly auth = inject(AuthService);
  private readonly router = inject(Router);

  readonly cargando = signal(false);
  readonly error = signal<string | null>(null);
  readonly exito = signal<string | null>(null);

  readonly roles: { value: RolUsuario; label: string }[] = [
    { value: 'estudiante', label: 'Estudiante' },
    { value: 'instructor', label: 'Instructor' }
  ];

  form = this.fb.group({
    nombres: ['', Validators.required],
    apellidos: ['', Validators.required],
    telefono: ['', Validators.required],
    correo: ['', [Validators.required, Validators.email]],
    password: ['', [Validators.required, Validators.minLength(6)]],
    ci: ['', Validators.required],
    extension: [''],
    f_nac: ['', Validators.required],
    sexo: ['M', Validators.required],
    estcivil: ['soltero', Validators.required],
    rol: ['estudiante' as RolUsuario, Validators.required]
  });

  enviar(): void {
    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.cargando.set(true);
    this.error.set(null);
    this.exito.set(null);

    const valores = this.form.getRawValue();

    this.auth
      .registrar({
        nombres: valores.nombres!,
        apellidos: valores.apellidos!,
        telefono: valores.telefono!,
        correo: valores.correo!,
        password: valores.password!,
        ci: valores.ci!,
        extension: valores.extension ?? '',
        f_nac: valores.f_nac!,
        sexo: valores.sexo as 'M' | 'F',
        estcivil: valores.estcivil as 'soltero' | 'casado',
        rol: valores.rol as RolUsuario
      })
      .subscribe({
        next: (respuesta) => {
          this.cargando.set(false);
          this.exito.set(respuesta.mensaje ?? 'Registro realizado correctamente.');
          setTimeout(() => this.router.navigate(['/login']), 1800);
        },
        error: (err) => {
          this.cargando.set(false);
          this.error.set(err?.error?.mensaje ?? 'No se pudo completar el registro.');
        }
      });
  }
}
