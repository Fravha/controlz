import { Component } from '@angular/core';

interface Integrante {
  nombre: string;
  rol: string;
  rolClase: string;
  foto: string;
  responsabilidad: string;
  frase: string;
  meta: { etiqueta: string; valor: string }[];
  links: { texto: string; url: string; externo: boolean }[];
}

@Component({
  selector: 'app-nosotros',
  standalone: true,
  template: `
    <div class="ns">
      <!-- Intro -->
      <header class="ns-hero">
        <img class="ns-hero-logo" src="img/imagen-02.jpg" alt="Logo Control Z"
             onerror="this.style.display='none'" />
        <div class="ns-eyebrow">Proyecto Formativo · Laboratorio UPDS</div>
        <h1>Nosotros</h1>
        <p class="ns-lead">
          Somos <strong>Control Z S.A.</strong> — cuatro estudiantes de Ingeniería de Sistemas
          construyendo una plataforma de cursos en línea con suscripciones y pagos automatizados,
          desde cero y con Scrum como método de trabajo.
        </p>
      </header>

      <!-- Filosofía -->
      <section class="ns-section">
        <div class="ns-head">
          <div class="ns-kicker">00 · Nuestra Empresa</div>
          <h2>Filosofía Institucional</h2>
        </div>
        <div class="ns-cards ns-cards-3">
          <div class="ns-card">
            <div class="ns-num">01</div><h5>Misión</h5>
            <p>Impulsar la formación académica y profesional en la Ingeniería de Sistemas mediante la integración de código eficiente, lógica estructurada y herramientas tecnológicas avanzadas, con una plataforma que optimice el aprendizaje y genere soluciones de alto impacto para la comunidad estudiantil.</p>
          </div>
          <div class="ns-card">
            <div class="ns-num">02</div><h5>Visión</h5>
            <p>Consolidarnos como la plataforma referente en gestión e innovación tecnológica universitaria, liderando la transformación digital de nuestros estudiantes y preparándolos para responder con solvencia a los desafíos tecnológicos globales.</p>
          </div>
          <div class="ns-card">
            <div class="ns-num">03</div><h5>Objetivo</h5>
            <p>Diseñar e implementar un entorno integral de herramientas digitales que facilite el acceso a recursos académicos, automatice procesos de gestión y potencie las habilidades de programación y lógica de los estudiantes.</p>
          </div>
        </div>
      </section>

      <!-- Integrantes -->
      <section class="ns-section">
        <div class="ns-head">
          <div class="ns-kicker">01 · Integrantes</div>
          <h2>Quiénes construyen esto</h2>
          <p class="ns-sub">Cuatro perfiles, cuatro formas de resolver problemas.</p>
        </div>

        <div class="ns-team">
          @for (m of equipo; track m.nombre) {
            <article class="ns-person">
              <div class="ns-photo">
                <img [src]="m.foto" [alt]="m.nombre" onerror="this.style.opacity='0'" />
                <span class="ns-pill" [class]="m.rolClase">{{ m.rol }}</span>
              </div>
              <div class="ns-person-body">
                <h3>{{ m.nombre }}</h3>
                <p class="ns-resp">{{ m.responsabilidad }}</p>
              </div>
              <details>
                <summary>Sobre {{ m.nombre.split(' ')[0] }}</summary>
                <div class="ns-details">
                  <p class="ns-quote">“{{ m.frase }}”</p>
                  <div class="ns-meta">
                    @for (it of m.meta; track it.etiqueta) {
                      <div><b>{{ it.etiqueta }}</b> {{ it.valor }}</div>
                    }
                  </div>
                  <div class="ns-links">
                    @for (l of m.links; track l.texto) {
                      <a [href]="l.url" [attr.target]="l.externo ? '_blank' : null"
                         [attr.rel]="l.externo ? 'noopener' : null">{{ l.texto }}</a>
                    }
                  </div>
                </div>
              </details>
            </article>
          }
        </div>
        <p class="ns-nota">Los roles pueden ser rotativos por sprint — cada integrante evidencia su aporte individual en cada entrega.</p>
      </section>

      <!-- Proyecto -->
      <section class="ns-section">
        <div class="ns-head">
          <div class="ns-kicker">02 · El proyecto</div>
          <h2>Plataforma de cursos en línea</h2>
          <p class="ns-sub">Suscripciones y pagos automatizados, de punta a punta.</p>
        </div>
        <div class="ns-compare">
          <div class="ns-col ns-old">
            <h4>Situación actual</h4>
            <ul>
              <li>Gestión en hojas de cálculo</li>
              <li>Activación lenta de planes</li>
              <li>Errores en permisos de cursos</li>
              <li>Poca visibilidad de ingresos</li>
            </ul>
          </div>
          <div class="ns-arrow">→</div>
          <div class="ns-col ns-new">
            <h4>Sistema propuesto</h4>
            <ul>
              <li>Portal web con roles</li>
              <li>Suscripciones por plan</li>
              <li>Pagos simulados y controlados</li>
              <li>Reportes para decidir mejor</li>
            </ul>
          </div>
        </div>
        <div class="ns-cards ns-cards-3" style="margin-top:26px">
          <div class="ns-card"><div class="ns-num">01</div><h5>Usuarios y roles</h5><p>Registro, login y permisos diferenciados por tipo de cuenta.</p></div>
          <div class="ns-card"><div class="ns-num">02</div><h5>Cursos y suscripciones</h5><p>Catálogo, planes, contratación, renovación e historial.</p></div>
          <div class="ns-card"><div class="ns-num">03</div><h5>Pagos y reportes</h5><p>Pasarela de prueba, confirmación y reportes de ingresos.</p></div>
        </div>
      </section>

      <footer class="ns-foot">
        <p class="ns-motto">Todo error tiene un <span>Ctrl+Z</span>. Nosotros construimos el resto.</p>
        <p class="ns-foot-sub">Equipo Control Z S.A. · Laboratorio UPDS · Agosto 2026</p>
      </footer>
    </div>
  `,
  styles: [`
    .ns { padding-bottom: 60px; }
    .ns-hero { text-align: center; padding: 64px 6vw 30px; max-width: 760px; margin: 0 auto; }
    .ns-hero-logo { width: 96px; height: 96px; border-radius: 16px; object-fit: cover; margin-bottom: 22px;
      filter: drop-shadow(0 0 30px rgba(79,124,255,0.35)); }
    .ns-eyebrow { font-family: var(--mono); font-size: 12px; letter-spacing: 0.25em; text-transform: uppercase;
      color: var(--cyan); margin-bottom: 14px; }
    .ns-hero h1 { font-family: var(--mono); font-weight: 800; font-size: clamp(34px, 6vw, 60px); margin: 0 0 14px; }
    .ns-lead { color: var(--muted); font-size: 16px; }
    .ns-lead strong { color: var(--text); }

    .ns-section { max-width: 1100px; margin: 0 auto; padding: 50px 6vw; }
    .ns-head { text-align: center; max-width: 640px; margin: 0 auto 40px; }
    .ns-kicker { font-family: var(--mono); font-size: 12px; letter-spacing: 0.25em; text-transform: uppercase;
      color: var(--cyan); margin-bottom: 12px; }
    .ns-head h2 { font-family: var(--mono); font-weight: 700; font-size: clamp(26px, 4vw, 42px); margin: 0 0 10px; }
    .ns-sub { color: var(--muted); margin: 0; }

    .ns-cards { display: grid; gap: 22px; }
    .ns-cards-3 { grid-template-columns: repeat(3, 1fr); }
    .ns-card { background: var(--panel); border: 1px solid var(--line); border-radius: var(--radius);
      padding: 24px; transition: transform .2s ease, border-color .2s ease; }
    .ns-card:hover { transform: translateY(-4px); border-color: var(--cyan); }
    .ns-num { font-family: var(--mono); font-size: 12px; color: var(--purple); margin-bottom: 10px; }
    .ns-card h5 { font-family: var(--mono); font-size: 16px; margin: 0 0 10px; }
    .ns-card p { color: var(--muted); font-size: 13.5px; line-height: 1.6; margin: 0; }

    .ns-team { display: grid; grid-template-columns: repeat(4, 1fr); gap: 22px; }
    .ns-person { background: var(--panel); border: 1px solid var(--line); border-radius: 18px; overflow: hidden;
      transition: transform .3s ease, border-color .3s ease; }
    .ns-person:hover { transform: translateY(-5px); border-color: rgba(148,163,196,0.35); }
    .ns-photo { position: relative; aspect-ratio: 1/1; overflow: hidden; background: var(--panel-2); }
    .ns-photo img { width: 100%; height: 100%; object-fit: cover; display: block;
      filter: grayscale(70%) brightness(0.9); transition: filter .4s ease, transform .4s ease; }
    .ns-person:hover .ns-photo img { filter: grayscale(0%) brightness(1); transform: scale(1.04); }
    .ns-pill { position: absolute; left: 12px; bottom: 12px; z-index: 2; font-family: var(--mono); font-size: 10px;
      letter-spacing: 0.08em; text-transform: uppercase; padding: 5px 11px; border-radius: 999px; font-weight: 600; }
    .role-po { background: rgba(163,91,255,0.22); color: #c7a4ff; border: 1px solid rgba(163,91,255,0.4); }
    .role-sm { background: rgba(79,124,255,0.22); color: #a9beff; border: 1px solid rgba(79,124,255,0.4); }
    .role-be { background: rgba(45,212,238,0.18); color: #8fe9f7; border: 1px solid rgba(45,212,238,0.4); }
    .role-fe { background: rgba(255,79,216,0.18); color: #ffb0ea; border: 1px solid rgba(255,79,216,0.4); }
    .ns-person-body { padding: 18px 18px 6px; }
    .ns-person-body h3 { font-family: var(--mono); font-size: 17px; margin: 0 0 4px; }
    .ns-resp { color: var(--muted); font-size: 13px; margin: 0; line-height: 1.5; }
    .ns-person details { border-top: 1px solid var(--line); margin-top: 14px; }
    .ns-person summary { list-style: none; cursor: pointer; display: flex; justify-content: space-between;
      align-items: center; padding: 13px 18px; font-family: var(--mono); font-size: 11px; letter-spacing: 0.08em;
      text-transform: uppercase; color: var(--muted); }
    .ns-person summary::-webkit-details-marker { display: none; }
    .ns-person summary::after { content: "+"; color: var(--cyan); font-size: 16px; }
    .ns-person details[open] summary::after { content: "–"; }
    .ns-details { padding: 0 18px 20px; }
    .ns-quote { font-style: italic; font-size: 13.5px; color: var(--text); border-left: 2px solid var(--cyan);
      padding-left: 12px; margin: 0 0 14px; line-height: 1.5; }
    .ns-meta { display: flex; flex-direction: column; gap: 7px; margin-bottom: 14px; }
    .ns-meta div { font-size: 12.5px; color: var(--muted); }
    .ns-meta b { color: var(--text); margin-right: 6px; }
    .ns-links { display: flex; gap: 10px; flex-wrap: wrap; }
    .ns-links a { font-family: var(--mono); font-size: 11px; font-weight: 600; padding: 7px 13px; border-radius: 8px;
      border: 1px solid var(--line); color: var(--text); transition: all .2s; }
    .ns-links a:hover { border-color: var(--cyan); color: var(--cyan); }
    .ns-nota { text-align: center; margin: 30px auto 0; font-family: var(--mono); font-size: 12.5px;
      color: var(--muted); border: 1px dashed var(--line); border-radius: 12px; padding: 14px 20px; }

    .ns-compare { display: grid; grid-template-columns: 1fr 50px 1fr; align-items: stretch; }
    .ns-col { background: var(--panel); border: 1px solid var(--line); border-radius: 16px; padding: 26px; }
    .ns-col h4 { font-family: var(--mono); font-size: 12px; letter-spacing: 0.12em; text-transform: uppercase; margin: 0 0 16px; }
    .ns-old h4 { color: #ff8a8a; }
    .ns-new h4 { color: var(--cyan); }
    .ns-col ul { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 11px; }
    .ns-col li { font-size: 14px; color: var(--muted); display: flex; gap: 9px; }
    .ns-old li::before { content: "×"; color: #ff8a8a; font-weight: 700; }
    .ns-new li::before { content: "✓"; color: var(--cyan); font-weight: 700; }
    .ns-arrow { display: flex; align-items: center; justify-content: center; font-size: 24px; color: var(--purple); }

    .ns-foot { text-align: center; padding: 60px 6vw 20px; }
    .ns-motto { font-family: var(--mono); font-weight: 700; font-size: clamp(18px, 3vw, 28px); margin: 0 0 12px; }
    .ns-motto span { background: linear-gradient(90deg, var(--purple), var(--magenta));
      -webkit-background-clip: text; background-clip: text; color: transparent; }
    .ns-foot-sub { font-family: var(--mono); font-size: 12px; color: var(--muted); }

    @media (max-width: 900px) {
      .ns-cards-3 { grid-template-columns: 1fr 1fr; }
      .ns-team { grid-template-columns: 1fr 1fr; }
      .ns-compare { grid-template-columns: 1fr; gap: 14px; }
      .ns-arrow { transform: rotate(90deg); padding: 4px 0; }
    }
    @media (max-width: 560px) {
      .ns-cards-3, .ns-team { grid-template-columns: 1fr; }
    }
  `]
})
export class NosotrosComponent {
  equipo: Integrante[] = [
    {
      nombre: 'Francisco Bailaba',
      rol: 'Product Owner',
      rolClase: 'ns-pill role-po',
      foto: 'img/imagen-03.jpg',
      responsabilidad: 'Prioriza el backlog, valida criterios de aceptación y comunica avances al equipo.',
      frase: 'Piensa dos veces, programa una vez.',
      meta: [
        { etiqueta: 'Carrera', valor: 'Estudiante de Ing. de Sistemas' },
        { etiqueta: 'Hobby', valor: 'Jugar COD' },
        { etiqueta: 'Sueño', valor: 'Tener 100 seguidores en LinkedIn' },
        { etiqueta: 'Estado civil', valor: 'Depende' }
      ],
      links: [{ texto: 'Perfil →', url: '#', externo: false }]
    },
    {
      nombre: 'Mario Arroyo',
      rol: 'Scrum Master',
      rolClase: 'ns-pill role-sm',
      foto: 'img/imagen-04.jpg',
      responsabilidad: 'Facilita las ceremonias, elimina impedimentos y asegura las prácticas ágiles del equipo.',
      frase: 'La imaginación es más importante que el conocimiento.',
      meta: [
        { etiqueta: 'Hobby', valor: 'Ir al campo' },
        { etiqueta: 'Sueño', valor: 'Dejar una huella significativa a través de su trabajo' },
        { etiqueta: 'Estado civil', valor: 'Complicado' }
      ],
      links: [{ texto: 'Perfil →', url: '#', externo: false }]
    },
    {
      nombre: 'Juan José Vicente',
      rol: 'Backend',
      rolClase: 'ns-pill role-be',
      foto: 'img/imagen-05.jpg',
      responsabilidad: 'Implementa la API REST, los modelos, las migraciones y la lógica de negocio.',
      frase: 'No es un bug, es una funcionalidad no documentada.',
      meta: [
        { etiqueta: 'Hobby', valor: 'Jugar fútbol (los 3 tiempos)' },
        { etiqueta: 'Sueño', valor: 'Terminar la carrera de una vez' },
        { etiqueta: 'Estado civil', valor: 'Casado' }
      ],
      links: [{ texto: 'Perfil →', url: '#', externo: false }]
    },
    {
      nombre: 'Allison Ramallo',
      rol: 'Frontend',
      rolClase: 'ns-pill role-fe',
      foto: 'img/imagen-06.jpg',
      responsabilidad: 'Implementa las vistas en Angular, los servicios de API, el estado y la experiencia de usuario.',
      frase: 'Convierto la creatividad en código y el código en experiencias.',
      meta: [
        { etiqueta: 'Carrera', valor: 'Estudiante de Ing. de Sistemas' },
        { etiqueta: 'Hobby', valor: 'Animación 2D e ilustración digital' },
        { etiqueta: 'Estado civil', valor: 'Vivir del arte' }
      ],
      links: [
        { texto: 'GitHub ↗', url: 'https://github.com/Hall-0/Allison_Ramallo', externo: true },
        { texto: 'LinkedIn ↗', url: 'https://www.linkedin.com/in/allison-ramallo-8b7482386', externo: true }
      ]
    }
  ];
}
