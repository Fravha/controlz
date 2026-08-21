import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { CursoPopular, EstudiantesActivosInactivos, IngresoMensual } from '../models/reporte.model';
import { urlControlador } from './php-api.util';


@Injectable({
  providedIn: 'root'
})
export class ReporteService {
  constructor(private http: HttpClient) {}

  ingresosMensuales(): Observable<IngresoMensual[]> {
    return this.http.get<IngresoMensual[]>(urlControlador('ReporteController', 'ingresosMensuales'));
  }

  cursosPopulares(): Observable<CursoPopular[]> {
    return this.http.get<CursoPopular[]>(urlControlador('ReporteController', 'cursosPopulares'));
  }

  estudiantesActivosInactivos(): Observable<EstudiantesActivosInactivos> {
    return this.http.get<EstudiantesActivosInactivos>(urlControlador('ReporteController', 'estudiantesActividad'));
  }
}
