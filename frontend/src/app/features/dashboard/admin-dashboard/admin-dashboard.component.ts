import { Component, OnInit, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../../../core/services/auth.service';
import { ReporteService } from '../../../core/services/reporte.service';
import { CursoPopular, EstudiantesActivosInactivos, IngresoMensual } from '../../../core/models/reporte.model';
import { UsuariosPanelComponent } from '../../admin/usuarios-panel/usuarios-panel.component';
import { Curso, NivelCurso } from '../../../core/models/curso.model';
import { PlanSuscripcion } from '../../../core/models/suscripcion.model';

type SeccionAdmin = 'usuarios' | 'cursos' | 'suscripciones' | 'pagos' | 'reportes';

let contadorCursoLocal = 0;

@Component({
  selector: 'app-admin-dashboard',
  standalone: true,
  imports: [FormsModule, UsuariosPanelComponent],
  templateUrl: './admin-dashboard.component.html',
  styleUrl: './admin-dashboard.component.css'
})
export class AdminDashboardComponent implements OnInit {
  readonly seccion = signal<SeccionAdmin>('usuarios');

  readonly ingresos = signal<IngresoMensual[]>([]);
  readonly cursosPopulares = signal<CursoPopular[]>([]);
  readonly actividad = signal<EstudiantesActivosInactivos | null>(null);
  readonly reportesDisponibles = signal(false);

  // El backend PHP todavía no tiene CursoController/SuscripcionController/
  // PagoController — estas listas viven solo en memoria del navegador
  // para poder probar los formularios mientras tanto. Quedan listas
  // para reemplazarse por llamadas reales cuando esos endpoints existan.
  readonly cursos = signal<Curso[]>([]);
  readonly planes = signal<PlanSuscripcion[]>([
    { id: 1, nombre: 'Basico', precio: 50, duracionDias: 30, maxCursos: 3 },
    { id: 2, nombre: 'Pro', precio: 120, duracionDias: 30, maxCursos: 10 },
    { id: 3, nombre: 'Premium', precio: 200, duracionDias: 30, maxCursos: null }
  ]);

  nuevoCurso: Partial<Curso> = this.cursoVacio();
  readonly niveles: NivelCurso[] = ['basico', 'intermedio', 'avanzado'];

  constructor(
    readonly auth: AuthService,
    private reporteService: ReporteService
  ) {}

  ngOnInit(): void {
    // Reportes obligatorios (mín. 3) definidos en la consigna: ingresos por
    // suscripción, cursos más inscritos, estudiantes activos vs inactivos.
    // ReporteController tampoco existe todavía en el backend PHP: si falla,
    // simplemente mostramos el estado "backend pendiente" en vez de romper.
    this.reporteService.ingresosMensuales().subscribe({
      next: (datos) => {
        this.ingresos.set(datos);
        this.reportesDisponibles.set(true);
      },
      error: () => {}
    });
    this.reporteService.cursosPopulares().subscribe({
      next: (datos) => this.cursosPopulares.set(datos),
      error: () => {}
    });
    this.reporteService.estudiantesActivosInactivos().subscribe({
      next: (datos) => this.actividad.set(datos),
      error: () => {}
    });
  }

  irA(seccion: SeccionAdmin): void {
    this.seccion.set(seccion);
  }

  agregarCurso(): void {
    if (!this.nuevoCurso.titulo || !this.nuevoCurso.nivel) {
      return;
    }

    contadorCursoLocal += 1;

    this.cursos.update((lista) => [
      ...lista,
      {
        id: contadorCursoLocal,
        titulo: this.nuevoCurso.titulo!,
        descripcion: this.nuevoCurso.descripcion ?? '',
        duracionHoras: Number(this.nuevoCurso.duracionHoras) || 0,
        nivel: this.nuevoCurso.nivel!,
        categoria: this.nuevoCurso.categoria ?? 'General',
        precioBase: Number(this.nuevoCurso.precioBase) || 0,
        planRequerido: this.nuevoCurso.planRequerido ?? null,
        instructorId: 0
      }
    ]);

    this.nuevoCurso = this.cursoVacio();
  }

  eliminarCurso(id: number): void {
    this.cursos.update((lista) => lista.filter((c) => c.id !== id));
  }

  private cursoVacio(): Partial<Curso> {
    return { titulo: '', descripcion: '', duracionHoras: 0, categoria: '', precioBase: 0 };
  }
}
