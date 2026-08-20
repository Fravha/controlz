<?php
session_start();

// Protección: solo entra quien inició sesión y es tipoper = 3 (Estudiante)
if (!isset($_SESSION['persona_id']) || (int) $_SESSION['persona_tipoper'] !== 3) {
    header('Location: ../index.html');
    exit;
}

$nombreCompleto = $_SESSION['persona_nombres'] . ' ' . $_SESSION['persona_apellidos'];

// Cursos gratuitos de ejemplo. Más adelante esto se reemplaza
// por una consulta real a la tabla de cursos (WHERE gratuito = 1).
$cursosGratuitos = array(
    array('titulo' => 'Introducción a la Programación', 'descripcion' => 'Fundamentos de lógica y primeros pasos con código.', 'duracion' => '4 semanas'),
    array('titulo' => 'HTML y CSS desde cero', 'descripcion' => 'Construye tus primeras páginas web paso a paso.', 'duracion' => '3 semanas'),
    array('titulo' => 'Introducción a Bases de Datos', 'descripcion' => 'Conceptos básicos de SQL y modelado de datos.', 'duracion' => '3 semanas'),
    array('titulo' => 'Introducción a Bases de Datos', 'descripcion' => 'Conceptos básicos de SQL y modelado de datos.', 'duracion' => '3 semanas'),
    array('titulo' => 'Introducción a Bases de Datos', 'descripcion' => 'Conceptos básicos de SQL y modelado de datos.', 'duracion' => '3 semanas'),
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel Estudiante — CTRL Z</title>
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
    <a href="#cursos-gratuitos"><span>02</span> Cursos gratuitos</a>
    <a href="#mis-cursos"><span>03</span> Mis cursos</a>
    <a href="../controllers/AuthController.php?action=logout" class="nav-action">
      <span>04</span> Cerrar sesión
    </a>
  </div>
</nav>
<!-- HERO -->
<div align="center">
  <h2>CTRL <span class="z">Z</span> ACADEMY</h2>
  <h4>Estudiante</h4>
    <p class="hero-sub">Bienvenido/a , <strong><?php echo htmlspecialchars($nombreCompleto); ?></strong>.</p> 
    <p class="sesion-activa"><span class="dot-online"></span>Sesión activa <?php echo htmlspecialchars($_SESSION['persona_correo']); ?></p>    
</div> 
<svg class="circuit" viewBox="0 0 1200 34" preserveAspectRatio="none">
  <path d="M0 17 H420 L450 5 H620 L650 17 H1200"/>
  <circle class="dot" cx="450" cy="5" r="3"/>
  <circle class="dot" cx="650" cy="17" r="3"/>
  <circle cx="1000" cy="17" r="4"/>
</svg>
<!-- CURSOS GRATUITOS -->
<section id="cursos-gratuitos">
  <div class="section-head">
    <div class="kicker">Estudiante · Catálogo</div>
    <h2>Cursos gratuitos</h2>
    <p>Inscríbete sin costo y empieza a aprender hoy mismo.</p>
  </div>
  <div class="project-shell">
    <div class="modules-grid">
      <?php foreach ($cursosGratuitos as $index => $curso): ?>
      <div class="module-card">
        <div class="module-num"><?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></div>
        <h5><?php echo htmlspecialchars($curso['titulo']); ?></h5>
        <p><?php echo htmlspecialchars($curso['descripcion']); ?></p>
        <p><em><?php echo htmlspecialchars($curso['duracion']); ?></em></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- MIS CURSOS -->
<section id="mis-cursos">
  <div class="section-head">
    <div class="kicker">Estudiante · Progreso</div>
    <h2>Mis cursos</h2>
    <p>Aún no estás inscrito/a en ningún curso.</p>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="motto-label">Panel de Estudiante</div>
  <p class="motto"><span>Ctrl+Z</span> ACADEMY.</p>
  <div class="foot-bottom">
    <span class="z">CTRL Z</span>
    <span>· Panel Estudiante · Equipo Control Z S.A.</span>
  </div>
</footer>

</div>
</body>
</html>
