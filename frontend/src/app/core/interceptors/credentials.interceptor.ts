import { HttpInterceptorFn } from '@angular/common/http';
import { environment } from '../../../environments/environment';

/**
 * El backend PHP (CTROL-Z) autentica con sesión de servidor (cookie),
 * no con un token JWT. Para que la cookie viaje en cada request hacia
 * ese backend hay que mandar `withCredentials: true`. Este interceptor
 * lo agrega automáticamente a cualquier request dirigida a phpApiUrl,
 * para no tener que repetirlo en cada servicio.
 */
export const credentialsInterceptor: HttpInterceptorFn = (req, next) => {
  if (!req.url.startsWith(environment.phpApiUrl)) {
    return next(req);
  }

  const clonada = req.clone({ withCredentials: true });
  return next(clonada);
};
