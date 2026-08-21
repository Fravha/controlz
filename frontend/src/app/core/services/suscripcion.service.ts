import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { PlanSuscripcion, Suscripcion } from '../models/suscripcion.model';
import { urlControlador } from './php-api.util';


@Injectable({
  providedIn: 'root'
})
export class SuscripcionService {
  constructor(private http: HttpClient) {}

  listarPlanes(): Observable<PlanSuscripcion[]> {
    return this.http.get<PlanSuscripcion[]>(urlControlador('SuscripcionController', 'planes'));
  }

  contratar(planId: number): Observable<Suscripcion> {
    return this.http.post<Suscripcion>(urlControlador('SuscripcionController', 'contratar'), { planId });
  }

  miSuscripcionActiva(): Observable<Suscripcion | null> {
    return this.http.get<Suscripcion | null>(urlControlador('SuscripcionController', 'activa'));
  }

  historial(): Observable<Suscripcion[]> {
    return this.http.get<Suscripcion[]>(urlControlador('SuscripcionController', 'historial'));
  }

  cancelarRenovacion(suscripcionId: number): Observable<Suscripcion> {
    return this.http.post<Suscripcion>(urlControlador('SuscripcionController', 'cancelarRenovacion'), {
      suscripcionId
    });
  }
}
