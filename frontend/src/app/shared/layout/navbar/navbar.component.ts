import { Component } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './navbar.component.html',
  styleUrl: './navbar.component.css'
})
export class NavbarComponent {
  constructor(
    readonly auth: AuthService,
    private router: Router
  ) {}

  cerrarSesion(): void {
    this.auth.logout();
    this.router.navigate(['/']);
  }

  rutaPanel(): string {
    const rol = this.auth.rol();
    if (rol === 'administrador') return '/panel/administrador';
    if (rol === 'instructor') return '/panel/instructor';
    return '/panel/estudiante';
  }
}
