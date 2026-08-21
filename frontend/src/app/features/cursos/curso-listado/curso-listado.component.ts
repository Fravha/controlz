import { Component, OnInit, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { CursoService } from '../../../core/services/curso.service';
import { Curso, CursoFiltro, NivelCurso } from '../../../core/models/curso.model';

@Component({
  selector: 'app-curso-listado',
  standalone: true,
  imports: [FormsModule, RouterLink],
  templateUrl: './curso-listado.component.html',
  styleUrl: './curso-listado.component.css'
})
export class CursoListadoComponent implements OnInit {
  readonly cursos = signal<Curso[]>([]);
  readonly cargando = signal(false);
  readonly error = signal<string | null>(null);

  readonly niveles: NivelCurso[] = ['basico', 'intermedio', 'avanzado'];

  filtro: CursoFiltro = {};

  constructor(private cursoService: CursoService) {}

  ngOnInit(): void {
    this.buscar();
  }

  buscar(): void {
    this.cargando.set(true);
    this.error.set(null);

    this.cursoService.listar(this.filtro).subscribe({
      next: (cursos) => {
        this.cursos.set(cursos);
        this.cargando.set(false);
      },
      error: () => {
        this.error.set('No se pudieron cargar los cursos. Intenta nuevamente.');
        this.cargando.set(false);
      }
    });
  }

  limpiarFiltros(): void {
    this.filtro = {};
    this.buscar();
  }
}
