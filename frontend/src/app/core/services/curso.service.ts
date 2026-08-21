import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Curso, CursoFiltro } from '../models/curso.model';
import { urlControlador } from './php-api.util';

@Injectable({
  providedIn: 'root'
})
export class CursoService {
  constructor(private http: HttpClient) {}

  listar(filtro: CursoFiltro = {}): Observable<Curso[]> {
    let params = new HttpParams();
    if (filtro.nivel) params = params.set('nivel', filtro.nivel);
    if (filtro.categoria) params = params.set('categoria', filtro.categoria);
    if (filtro.precioMax != null) params = params.set('precio_max', filtro.precioMax);
    if (filtro.q) params = params.set('q', filtro.q);

    return this.http.get<Curso[]>(urlControlador('CursosController', 'listar'), { params });
  }

  obtener(id: number): Observable<Curso> {
    return this.http.get<Curso>(urlControlador('CursosController', 'detalle'), {
      params: { id }
    });
  }

  crear(curso: Partial<Curso>): Observable<Curso> {
    return this.http.post<Curso>(urlControlador('CursosController', 'crear'), curso);
  }

  actualizar(id: number, curso: Partial<Curso>): Observable<Curso> {
    return this.http.post<Curso>(urlControlador('CursosController', 'actualizar'), { id, ...curso });
  }

  eliminar(id: number): Observable<void> {
    return this.http.post<void>(urlControlador('CursosController', 'eliminar'), { id });
  }

  misCursos(): Observable<Curso[]> {
    // Cursos creados por el instructor autenticado (según la sesión PHP).
    return this.http.get<Curso[]>(urlControlador('CursosController', 'mios'));
  }
}
