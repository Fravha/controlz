import { Component } from '@angular/core';

interface Articulo {
  etiqueta: string;
  titulo: string;
  extracto: string;
  fecha: string;
  autor: string;
}

@Component({
  selector: 'app-blog',
  standalone: true,
  template: `
    <section class="blog">
      <div class="container">
        <div class="blog-head">
          <div class="kicker">CTRL Z · BLOG</div>
          <h1>Bitácora del equipo</h1>
          <p>Avances del proyecto, decisiones técnicas y aprendizajes de cada sprint.</p>
        </div>

        <div class="blog-grid">
          @for (art of articulos; track art.titulo) {
            <article class="post">
              <span class="post-tag">{{ art.etiqueta }}</span>
              <h3>{{ art.titulo }}</h3>
              <p class="post-extracto">{{ art.extracto }}</p>
              <div class="post-meta">
                <span>{{ art.autor }}</span>
                <span>{{ art.fecha }}</span>
              </div>
            </article>
          }
        </div>

        <p class="blog-nota">Más entradas próximamente. Esta sección se irá completando sprint a sprint.</p>
      </div>
    </section>
  `,
  styles: [`
    .blog { padding: 60px 0 90px; }
    .blog-head { max-width: 640px; margin: 0 auto 46px; text-align: center; }
    .blog-head .kicker {
      font-family: var(--mono); font-size: 12px; letter-spacing: 0.25em;
      text-transform: uppercase; color: var(--cyan); margin-bottom: 12px;
    }
    .blog-head h1 { font-family: var(--mono); font-weight: 800; font-size: clamp(28px, 4vw, 44px); margin: 0 0 10px; }
    .blog-head p { color: var(--muted); margin: 0; }

    .blog-grid {
      display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 22px;
    }
    .post {
      background: var(--panel); border: 1px solid var(--line); border-radius: var(--radius);
      padding: 24px; display: flex; flex-direction: column; gap: 12px;
      transition: transform .2s ease, border-color .2s ease;
    }
    .post:hover { transform: translateY(-4px); border-color: var(--cyan); }
    .post-tag {
      align-self: flex-start; font-family: var(--mono); font-size: 10.5px; letter-spacing: 0.1em;
      text-transform: uppercase; color: #c7a4ff; background: rgba(163,91,255,0.16);
      border: 1px solid rgba(163,91,255,0.4); padding: 5px 11px; border-radius: 999px;
    }
    .post h3 { font-family: var(--mono); font-size: 18px; margin: 0; }
    .post-extracto { color: var(--muted); font-size: 14px; margin: 0; flex: 1; }
    .post-meta {
      display: flex; justify-content: space-between; font-family: var(--mono);
      font-size: 12px; color: var(--muted); border-top: 1px solid var(--line); padding-top: 12px;
    }
    .blog-nota {
      text-align: center; margin: 40px auto 0; color: var(--muted); font-family: var(--mono);
      font-size: 13px; border: 1px dashed var(--line); border-radius: 12px; padding: 16px 22px; max-width: 640px;
    }
  `]
})
export class BlogComponent {
  articulos: Articulo[] = [
    {
      etiqueta: 'Sprint 1',
      titulo: 'Arrancamos: backlog y arquitectura',
      extracto: 'Definimos el alcance mínimo, priorizamos el backlog y elegimos la arquitectura: PHP + MySQL en el backend y Angular en el frontend.',
      fecha: 'Ago 2026',
      autor: 'Equipo Control Z'
    },
    {
      etiqueta: 'Backend',
      titulo: 'Autenticación con sesión y roles',
      extracto: 'Registro y login contra la API PHP, contraseñas hasheadas y permisos diferenciados para administrador, instructor y estudiante.',
      fecha: 'Ago 2026',
      autor: 'Juan José Vicente'
    },
    {
      etiqueta: 'Frontend',
      titulo: 'La interfaz en Angular',
      extracto: 'Componentes standalone, servicios de consumo de API y manejo de estado con signals para una experiencia fluida.',
      fecha: 'Ago 2026',
      autor: 'Allison Ramallo'
    }
  ];
}
