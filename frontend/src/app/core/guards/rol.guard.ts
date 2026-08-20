import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';
import { RolUsuario } from '../models/usuario.model';

/**
 * Restringe una ruta a uno o más roles.
 * Uso en app.routes.ts: canActivate: [authGuard, rolGuard(['administrador'])]
 */
export const rolGuard = (rolesPermitidos: RolUsuario[]): CanActivateFn => {
  return () => {
    const auth = inject(AuthService);
    const router = inject(Router);
    const rolActual = auth.rol();

    if (rolActual && rolesPermitidos.includes(rolActual)) {
      return true;
    }

    router.navigate(['/']);
    return false;
  };
};
