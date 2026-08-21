import { Routes } from '@angular/router';
import { HomeComponent } from './features/home/home/home.component';
import { LoginComponent } from './features/auth/login/login.component';
import { RegistroComponent } from './features/auth/registro/registro.component';
import { CursoListadoComponent } from './features/cursos/curso-listado/curso-listado.component';
import { CursoDetalleComponent } from './features/cursos/curso-detalle/curso-detalle.component';
import { BlogComponent } from './features/blog/blog.component';
import { NosotrosComponent } from './features/nosotros/nosotros.component';
import { AdminDashboardComponent } from './features/dashboard/admin-dashboard/admin-dashboard.component';
import { InstructorDashboardComponent } from './features/dashboard/instructor-dashboard/instructor-dashboard.component';
import { EstudianteDashboardComponent } from './features/dashboard/estudiante-dashboard/estudiante-dashboard.component';
import { authGuard } from './core/guards/auth.guard';
import { rolGuard } from './core/guards/rol.guard';

export const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'login', component: LoginComponent },
  { path: 'registro', component: RegistroComponent },
  { path: 'cursos', component: CursoListadoComponent },
  { path: 'cursos/:id', component: CursoDetalleComponent },
  { path: 'blog', component: BlogComponent },
  { path: 'nosotros', component: NosotrosComponent },
  {
    path: 'panel/administrador',
    component: AdminDashboardComponent,
    canActivate: [authGuard, rolGuard(['administrador'])]
  },
  {
    path: 'panel/instructor',
    component: InstructorDashboardComponent,
    canActivate: [authGuard, rolGuard(['instructor'])]
  },
  {
    path: 'panel/estudiante',
    component: EstudianteDashboardComponent,
    canActivate: [authGuard, rolGuard(['estudiante'])]
  },
  { path: '**', redirectTo: '' }
];
