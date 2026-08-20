import { Component, OnInit, signal } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { CursoService } from '../../../core/services/curso.service';
import { AuthService } from '../../../core/services/auth.service';
import { Curso } from '../../../core/models/curso.model';

@Component({
  selector: 'app-curso-detalle',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './curso-detalle.component.html',
  styleUrl: './curso-detalle.component.css'
})
export class CursoDetalleComponent implements OnInit {
  readonly curso = signal<Curso | null>(null);
  readonly cargando = signal(true);
  readonly error = signal<string | null>(null);

  constructor(
    private route: ActivatedRoute,
    private cursoService: CursoService,
    readonly auth: AuthService
  ) {}

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    if (!id) {
      this.error.set('Curso no encontrado.');
      this.cargando.set(false);
      return;
    }

    this.cursoService.obtener(id).subscribe({
      next: (curso) => {
        this.curso.set(curso);
        this.cargando.set(false);
      },
      error: () => {
        this.error.set('No se pudo cargar el detalle del curso.');
        this.cargando.set(false);
      }
    });
  }
}
