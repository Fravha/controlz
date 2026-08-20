import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './home.component.html',
  styleUrl: './home.component.css'
})
export class HomeComponent {
  readonly modulos = [
    { num: '01', titulo: 'Usuarios', desc: 'Registro, login y roles con permisos diferenciados.' },
    { num: '02', titulo: 'Cursos', desc: 'Listado público, filtros, detalle y acceso restringido.' },
    { num: '03', titulo: 'Suscripciones', desc: 'Planes, contratación, renovación e historial.' },
    { num: '04', titulo: 'Pagos', desc: 'Pasarela de prueba, transacciones y confirmación.' },
    { num: '05', titulo: 'Reportes', desc: 'Ingresos, cursos top y estudiantes activos.' }
  ];
}
