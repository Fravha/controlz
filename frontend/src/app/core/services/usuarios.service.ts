import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';
import { RolUsuario, ROL_A_TIPOPER, tipoperARol } from '../models/usuario.model';
import { urlControlador } from './php-api.util';

export interface PersonaListada {
  id: number;
  nombres: string;
  apellidos: string;
  correo: string;
  tipoper: number;
  estado: number;
  rol: RolUsuario | null;
}

interface RespuestaListar {
  ok: boolean;
  data: Omit<PersonaListada, 'rol'>[];
  error?: string;
}

interface RespuestaActualizar {
  ok: boolean;
  actualizado: boolean;
  error?: string;
}


@Injectable({
  providedIn: 'root'
})
export class UsuariosService {
  constructor(private http: HttpClient) {}

  listar(): Observable<PersonaListada[]> {
    return this.http.get<RespuestaListar>(urlControlador('UsuariosController', 'listar')).pipe(
      map((respuesta) =>
        respuesta.data.map((p) => ({ ...p, rol: tipoperARol(p.tipoper) }))
      )
    );
  }

  actualizarRol(id: number, rol: RolUsuario): Observable<boolean> {
    const body = { id, tipoper: ROL_A_TIPOPER[rol] };

    return this.http
      .post<RespuestaActualizar>(urlControlador('UsuariosController', 'actualizarTipo'), body)
      .pipe(map((respuesta) => !!respuesta.actualizado));
  }
}
