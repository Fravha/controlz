import { environment } from '../../../environments/environment';


export function urlControlador(controlador: string, accion: string): string {
  return `${environment.phpApiUrl}/controllers/${controlador}.php?action=${accion}`;
}

/** Los controllers de auth (login/registro) reciben application/x-www-form-urlencoded, no JSON. */
export function comoFormUrlEncoded(datos: Record<string, string | number | undefined>): string {
  const params = new URLSearchParams();
  Object.entries(datos).forEach(([clave, valor]) => {
    if (valor !== undefined) {
      params.set(clave, String(valor));
    }
  });
  return params.toString();
}

export const HEADERS_FORM_URLENCODED = {
  'Content-Type': 'application/x-www-form-urlencoded'
};
