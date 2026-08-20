import { Component, OnInit, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';
import { SuscripcionService } from '../../../core/services/suscripcion.service';
import { CursoService } from '../../../core/services/curso.service';
import { Suscripcion } from '../../../core/models/suscripcion.model';
import { Curso } from '../../../core/models/curso.model';

type SeccionEstudiante = 'cursos-gratuitos' | 'suscripcion' | 'mis-cursos';

// Igual que en admin/instructor: mientras no exista CursoController,
// mostramos los mismos cursos de ejemplo que hoy están hardcodeados
// en estudiante/index.php.
const CURSOS_GRATUITOS_EJEMPLO: Curso[] = [
  {
    id: 1,
    titulo: 'Introducción a la Programación',
    descripcion: 'Fundamentos de lógica y primeros pasos con código.',
    duracionHoras: 20,
    nivel: 'basico',
    categoria: 'Programación',
    precioBase: 0,
    planRequerido: null,
    instructorId: 0
  },
  {
    id: 2,
    titulo: 'HTML y CSS desde cero',
    descripcion: 'Construye tus primeras páginas web paso a paso.',
    duracionHoras: 15,
    nivel: 'basico',
    categoria: 'Frontend',
    precioBase: 0,
    planRequerido: null,
    instructorId: 0
  },
  {
    id: 3,
    titulo: 'Introducción a Bases de Datos',
    descripcion: 'Conceptos básicos de SQL y modelado de datos.',
    duracionHoras: 12,
    nivel: 'basico',
    categoria: 'Bases de datos',
    precioBase: 0,
    planRequerido: null,
    instructorId: 0
  }
];

@Component({
  selector: 'app-estudiante-dashboard',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './estudiante-dashboard.component.html',
  styleUrl: './estudiante-dashboard.component.css'
})
export class EstudianteDashboardComponent implements OnInit {
  readonly seccion = signal<SeccionEstudiante>('cursos-gratuitos');

  readonly suscripcion = signal<Suscripcion | null>(null);
  readonly cargandoSuscripcion = signal(true);
  readonly suscripcionDesdeBackend = signal(false);

  readonly cursosGratuitos = signal<Curso[]>([]);
  readonly cursosDesdeBackend = signal(false);

  constructor(
    readonly auth: AuthService,
    private suscripcionService: SuscripcionService,
    private cursoService: CursoService
  ) {}

  ngOnInit(): void {
    this.suscripcionService.miSuscripcionActiva().subscribe({
      next: (s) => {
        this.suscripcion.set(s);
        this.suscripcionDesdeBackend.set(true);
        this.cargandoSuscripcion.set(false);
      },
      error: () => this.cargandoSuscripcion.set(false)
    });

    this.cursoService.listar().subscribe({
      next: (cursos) => {
        this.cursosGratuitos.set(cursos);
        this.cursosDesdeBackend.set(true);
      },
      error: () => {
        this.cursosGratuitos.set(CURSOS_GRATUITOS_EJEMPLO);
        this.cursosDesdeBackend.set(false);
      }
    });
  }

  irA(seccion: SeccionEstudiante): void {
    this.seccion.set(seccion);
  }
}
