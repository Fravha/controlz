import { Component, OnInit, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../../../core/services/auth.service';
import { CursoService } from '../../../core/services/curso.service';
import { Curso } from '../../../core/models/curso.model';

type SeccionDocente = 'cursos' | 'estudiantes' | 'calificaciones';

interface EstudianteInscrito {
  nombre: string;
  correo: string;
  curso: string;
  progreso: number;
}

interface Calificacion {
  id: number;
  curso: string;
  estudiante: string;
  nota: number;
  comentario: string;
  fecha: string;
}

let contadorCalificacion = 0;

// Mientras no exista CursoController en el backend, mostramos los
// mismos cursos de ejemplo que hoy están hardcodeados en docente/index.php,
// para que el panel no se vea vacío en la demo.
const CURSOS_EJEMPLO: Curso[] = [
  {
    id: 1,
    titulo: 'Introducción a la Programación',
    descripcion: 'Fundamentos de lógica y primeros pasos con código.',
    duracionHoras: 20,
    nivel: 'basico',
    categoria: 'Programación',
    precioBase: 0,
    planRequerido: null,
    instructorId: 0,
    inscritos: 24
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
    instructorId: 0,
    inscritos: 18
  },
  {
    id: 3,
    titulo: 'Bases de Datos Avanzadas',
    descripcion: 'Modelado, normalización y consultas complejas.',
    duracionHoras: 30,
    nivel: 'avanzado',
    categoria: 'Bases de datos',
    precioBase: 0,
    planRequerido: 'pro',
    instructorId: 0,
    inscritos: 0
  }
];

const ESTUDIANTES_EJEMPLO: EstudianteInscrito[] = [
  { nombre: 'Sofía Mamani', correo: 'sofia.mamani@correo.com', curso: 'Introducción a la Programación', progreso: 65 },
  { nombre: 'Marcelo Quispe', correo: 'marcelo.quispe@correo.com', curso: 'Introducción a la Programación', progreso: 40 },
  { nombre: 'Valeria Rojas', correo: 'valeria.rojas@correo.com', curso: 'HTML y CSS desde cero', progreso: 90 }
];

@Component({
  selector: 'app-instructor-dashboard',
  standalone: true,
  imports: [FormsModule],
  templateUrl: './instructor-dashboard.component.html',
  styleUrl: './instructor-dashboard.component.css'
})
export class InstructorDashboardComponent implements OnInit {
  readonly seccion = signal<SeccionDocente>('cursos');

  readonly misCursos = signal<Curso[]>([]);
  readonly cargandoCursos = signal(true);
  readonly cursosDesdeBackend = signal(false);

  readonly estudiantes = signal<EstudianteInscrito[]>(ESTUDIANTES_EJEMPLO);

  readonly calificaciones = signal<Calificacion[]>([]);
  nuevaCalificacion = { curso: '', estudiante: '', nota: null as number | null, comentario: '' };

  constructor(
    readonly auth: AuthService,
    private cursoService: CursoService
  ) {}

  ngOnInit(): void {
    this.cursoService.misCursos().subscribe({
      next: (cursos) => {
        this.misCursos.set(cursos);
        this.cursosDesdeBackend.set(true);
        this.cargandoCursos.set(false);
      },
      error: () => {
        // CursoController todavía no existe: mostramos los cursos de
        // ejemplo para no dejar la sección vacía en la demo del sprint.
        this.misCursos.set(CURSOS_EJEMPLO);
        this.cursosDesdeBackend.set(false);
        this.cargandoCursos.set(false);
      }
    });
  }

  irA(seccion: SeccionDocente): void {
    this.seccion.set(seccion);
  }

  registrarCalificacion(): void {
    if (!this.nuevaCalificacion.curso || !this.nuevaCalificacion.estudiante || this.nuevaCalificacion.nota == null) {
      return;
    }

    contadorCalificacion += 1;

    this.calificaciones.update((lista) => [
      {
        id: contadorCalificacion,
        curso: this.nuevaCalificacion.curso,
        estudiante: this.nuevaCalificacion.estudiante,
        nota: this.nuevaCalificacion.nota!,
        comentario: this.nuevaCalificacion.comentario,
        fecha: new Date().toISOString().slice(0, 10)
      },
      ...lista
    ]);

    this.nuevaCalificacion = { curso: '', estudiante: '', nota: null, comentario: '' };
  }
}
