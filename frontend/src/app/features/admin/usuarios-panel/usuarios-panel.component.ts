import { Component, OnInit, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { PersonaListada, UsuariosService } from '../../../core/services/usuarios.service';
import { RolUsuario } from '../../../core/models/usuario.model';

interface FilaUsuario extends PersonaListada {
  rolSeleccionado: RolUsuario;
  guardando: boolean;
  guardado: boolean;
  error: boolean;
}

@Component({
  selector: 'app-usuarios-panel',
  standalone: true,
  imports: [FormsModule],
  templateUrl: './usuarios-panel.component.html',
  styleUrl: './usuarios-panel.component.css'
})
export class UsuariosPanelComponent implements OnInit {
  readonly filas = signal<FilaUsuario[]>([]);
  readonly cargando = signal(true);
  readonly error = signal<string | null>(null);

  readonly roles: { value: RolUsuario; label: string }[] = [
    { value: 'administrador', label: 'Administrador' },
    { value: 'instructor', label: 'Docente' },
    { value: 'estudiante', label: 'Estudiante' }
  ];

  constructor(private usuariosService: UsuariosService) {}

  ngOnInit(): void {
    this.cargar();
  }

  cargar(): void {
    this.cargando.set(true);
    this.error.set(null);

    this.usuariosService.listar().subscribe({
      next: (personas) => {
        this.filas.set(
          personas.map((p) => ({
            ...p,
            rolSeleccionado: p.rol ?? 'estudiante',
            guardando: false,
            guardado: false,
            error: false
          }))
        );
        this.cargando.set(false);
      },
      error: () => {
        this.error.set('No se pudo cargar la lista de usuarios. ¿El backend PHP está corriendo?');
        this.cargando.set(false);
      }
    });
  }

  confirmar(fila: FilaUsuario): void {
    fila.guardando = true;
    fila.guardado = false;
    fila.error = false;

    this.usuariosService.actualizarRol(fila.id, fila.rolSeleccionado).subscribe({
      next: (actualizado) => {
        fila.guardando = false;
        fila.guardado = actualizado;
        fila.error = !actualizado;
        if (actualizado) {
          setTimeout(() => (fila.guardado = false), 1600);
        }
      },
      error: () => {
        fila.guardando = false;
        fila.error = true;
      }
    });
  }
}
