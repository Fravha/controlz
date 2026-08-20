<?php
session_start();
// Protección: solo entra quien inició sesión y es tipoper = 2 (Docente)
if (!isset($_SESSION['persona_id']) || (int) $_SESSION['persona_tipoper'] !== 2) {
    header('Location: ../index.html');
    exit;
}
$nombreCompleto = $_SESSION['persona_nombres'] . ' ' . $_SESSION['persona_apellidos'];
// Cursos de ejemplo a cargo del docente. Más adelante esto se reemplaza
// por una consulta real (WHERE id_docente = $_SESSION['persona_id']).
$misCursos = array(
    array('titulo' => 'Introducción a la Programación', 'estudiantes' => 24, 'estado' => 'Activo'),
    array('titulo' => 'HTML y CSS desde cero', 'estudiantes' => 18, 'estado' => 'Activo'),
    array('titulo' => 'Bases de Datos Avanzadas', 'estudiantes' => 0, 'estado' => 'Borrador'),
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel Docente — CTRL Z</title>
<link rel="stylesheet" href="../css/estilos.css">
</head>
<body>

<svg width="0" height="0">
  <defs>
    <linearGradient id="circGrad" x1="0" y1="0" x2="1" y2="0">
      <stop offset="0%" stop-color="#2dd4ee"/>
      <stop offset="50%" stop-color="#4f7cff"/>
      <stop offset="100%" stop-color="#a35bff"/>
    </linearGradient>
  </defs>
</svg>

<div class="wrap">

<nav>
  <div class="brand">
    <img src="../img/imagen-01.jpg" alt="Control Z">
    CTRL<span class="z">Z</span>
  </div>

  <div class="navlinks">
    <a href="#inicio"><span>01</span> Inicio</a>
    <a href="#mis-cursos"><span>02</span> Mis cursos</a>
    <a href="#estudiantes"><span>03</span> Estudiantes</a>
    <a href="#calificaciones"><span>04</span> Calificaciones</a>

    <a href="../controllers/AuthController.php?action=logout" class="nav-action">
      <span>05</span> Cerrar sesión
    </a>
  </div>
</nav>

<!-- HERO -->
<div align="center">  
  <h2>Panel Docente</h2>
  <p class="hero-sub">Bienvenid@, <strong><?php echo htmlspecialchars($nombreCompleto); ?></strong></p>  
     <p class="sesion-activa"><span class="dot-online"></span>Sesión activa · <?php echo htmlspecialchars($_SESSION['persona_correo']); ?></p>
 
</div>

<svg class="circuit" viewBox="0 0 1200 34" preserveAspectRatio="none">
  <path d="M0 17 H420 L450 5 H620 L650 17 H1200"/>
  <circle class="dot" cx="450" cy="5" r="3"/>
  <circle class="dot" cx="650" cy="17" r="3"/>
  <circle cx="1000" cy="17" r="4"/>
</svg>

<!-- MIS CURSOS -->
<section id="mis-cursos">
  <div class="section-head">
    <div class="kicker">Docente · Cursos a cargo</div>
    <h2>Mis cursos</h2>
    <p>Cursos que estás dictando actualmente.</p>
  </div>

  <div class="project-shell">
    <div class="modules-grid">
      <?php foreach ($misCursos as $index => $curso): ?>
      <div class="module-card">
        <div class="module-num"><?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></div>
        <h5><?php echo htmlspecialchars($curso['titulo']); ?></h5>
        <p><?php echo (int) $curso['estudiantes']; ?> estudiante(s) inscrito(s).</p>
        <p><em><?php echo htmlspecialchars($curso['estado']); ?></em></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ESTUDIANTES -->
<section id="estudiantes">
  <div class="section-head">
    <div class="kicker">Docente · Seguimiento</div>
    <h2>Estudiantes</h2>
    <p>Aquí verás el listado de estudiantes inscritos en tus cursos.</p>
  </div>
</section>

<!-- CALIFICACIONES -->
<section id="calificaciones">
  <div class="section-head">
    <div class="kicker">Docente · Evaluación</div>
    <h2>Calificaciones</h2>
    <p>Aquí podrás registrar y revisar calificaciones por curso.</p>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="motto-label">Panel de Docente</div>
  <p class="motto"><span>Ctrl+Z</span> ACADEMY.</p>
  <div class="foot-bottom">
    <span class="z">CTRL Z</span>
    <span>· Panel Docente · Equipo Control Z S.A.</span>
  </div>
</footer>

</div>
</body>
</html>
