import { ApplicationConfig, inject, provideAppInitializer, provideZoneChangeDetection } from '@angular/core';
import { provideRouter } from '@angular/router';
import { provideHttpClient, withInterceptors } from '@angular/common/http';
import { firstValueFrom } from 'rxjs';

import { routes } from './app.routes';
import { credentialsInterceptor } from './core/interceptors/credentials.interceptor';
import { AuthService } from './core/services/auth.service';

export const appConfig: ApplicationConfig = {
  providers: [
    provideZoneChangeDetection({ eventCoalescing: true }),
    provideRouter(routes),
    provideHttpClient(withInterceptors([credentialsInterceptor])),
    // Antes de que la app renderice nada, preguntamos al backend si ya
    // hay una sesión de PHP abierta (por ejemplo, tras recargar F5).
    // Así los guards de rol no expulsan a un usuario que sí está logueado.
    provideAppInitializer(() => {
      const auth = inject(AuthService);
      return firstValueFrom(auth.restaurarSesion());
    })
  ]
};
