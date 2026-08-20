import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { IniciarPagoRequest, IniciarPagoResponse, Pago } from '../models/pago.model';
import { urlControlador } from './php-api.util';


@Injectable({
  providedIn: 'root'
})
export class PagoService {
  constructor(private http: HttpClient) {}

  iniciarPago(datos: IniciarPagoRequest): Observable<IniciarPagoResponse> {

    return this.http.post<IniciarPagoResponse>(urlControlador('PagosController', 'iniciar'), datos);
  }

  confirmar(transaccionId: string): Observable<Pago> {
    return this.http.post<Pago>(urlControlador('PagosController', 'confirmar'), { transaccionId });
  }

  misPagos(): Observable<Pago[]> {
    return this.http.get<Pago[]>(urlControlador('PagosController', 'mios'));
  }
}
