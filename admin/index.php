<?php
session_start();

// Protección: solo entra quien inició sesión y es tipoper = 1 (Administrador)
if (!isset($_SESSION['persona_id']) || (int) $_SESSION['persona_tipoper'] !== 1) {
    header('Location: ../index.html');
    exit;
}
$nombreCompleto = $_SESSION['persona_nombres'] . ' ' . $_SESSION['persona_apellidos'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel Administrador — CTRL Z</title>
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
    <a href="#inico"><?php echo htmlspecialchars($nombreCompleto); ?></a>     
    <a href="../controllers/AuthController.php?action=logout" class="nav-action">
      <span></span> Cerrar sesión
    </a>
  </div> 
</nav>
<!-- HERO -->
<div align="center">
    <h2>Panel Administrador</h2>  
    <p class="hero-sub">Bienvenid@, <strong><?php echo htmlspecialchars($nombreCompleto); ?></strong></p>
    <p class="sesion-activa">
        <span class="dot-online"></span>
        Sesión activa
    </p>
    <p><?php echo htmlspecialchars($_SESSION['persona_correo']); ?></p>
</div>
<svg class="circuit" viewBox="0 0 1200 34" preserveAspectRatio="none">
  <path d="M0 17 H420 L450 5 H620 L650 17 H1200"/>
  <circle class="dot" cx="450" cy="5" r="3"/>
  <circle class="dot" cx="650" cy="17" r="3"/>
  <circle cx="1000" cy="17" r="4"/>
</svg>
<!-- MÓDULOS -->
<section id="alcance">
  <div class="section-head">
    <div class="kicker">Panel · Módulos</div>
    <h2>Gestión de la plataforma</h2>
    <p>Accesos rápidos a cada área del sistema.</p>
  </div>
  <div class="project-shell">
    <div class="modules-grid">
      <a class="module-card" href="#usuarios" id="usuarios">
        <div class="module-num">01</div>
        <h5>Usuarios</h5>
        <p>Administrar roles, modificar datos y activar o desactivar usuario.</p>
      </a>
      <a class="module-card" href="#cursos" id="cursos">
        <div class="module-num">02</div>
        <h5>Cursos</h5>
        <p>Crear, editar y publicar cursos disponibles.</p>
      </a>
      <a class="module-card" href="#suscripciones" id="suscripciones">
        <div class="module-num">03</div>
        <h5>Suscripciones</h5>
        <p>Planes activos, renovaciones e historial.</p>
      </a>
      <a class="module-card" href="#pagos" id="pagos">
        <div class="module-num">04</div>
        <h5>Pagos</h5>
        <p>Transacciones, confirmaciones y estado de pagos.</p>
      </a>
      <a class="module-card" href="#reportes" id="reportes">
        <div class="module-num">05</div>
        <h5>Reportes</h5>
        <p>Ingresos, cursos top y estudiantes activos.</p>
      </a>
    </div>
  </div>
</section>
<!-- FOOTER -->
<footer>
  <div class="motto-label">Panel de Administrador</div>
  <p class="motto"><span>Ctrl+Z</span> ACADEMY.</p>
  <div class="foot-bottom">
    <span class="z">CTRL Z</span>
    <span>· Panel Administrador · Equipo Control Z S.A.</span>
  </div>
</footer>
</div>
<!-- MODAL: Gestión de usuarios -->
<div class="modal-overlay" id="modalUsuarios">
  <div class="modal">
    <button type="button" class="modal-close" id="cerrarModalUsuarios">&times;</button>
    <div class="modal-header">
      <div class="modal-kicker">Panel · Usuarios</div>
      <h2>Usuarios registrados</h2>
      <p>Cambia el rol y confirma para actualizar en el sistema.</p>
    </div>
    <div class="modal-status" id="usuariosStatus">Cargando...</div>
    <div class="usuarios-list" id="usuariosList"></div>
  </div>
</div>
<!-- MODAL: Editar datos de una persona -->
<div class="modal-overlay" id="modalEditarUsuario">
  <div class="modal">
    <button type="button" class="modal-close" id="cerrarModalEditar">&times;</button>
    <div class="modal-header">
      <div class="modal-kicker">Panel · Usuarios</div>
      <h2>Modificar datos</h2>
      <p>Actualiza la información de la persona. El correo debe ser único.</p>
    </div>
    <form class="form-editar" id="formEditarUsuario">
      <input type="hidden" id="editId" value="">
      <div class="form-editar-row">
        <div>
          <label for="editNombres">Nombres:</label>
          <input type="text" id="editNombres" required>
        </div>
        <div>
          <label for="editApellidos">Apellidos:</label>
          <input type="text" id="editApellidos" required>
        </div>
      </div>
      <div class="form-editar-row">
        <div>
          <label for="editFNac">Fecha Nacimiento:</label>
          <input type="date" id="editFNac" required>
        </div>
        <div>
          <label>Sexo:</label>
          <div class="radio-group">
            <label><input type="radio" name="editSexo" value="M"> Masculino</label>
            <label><input type="radio" name="editSexo" value="F"> Femenino</label>
          </div>
        </div>
      </div>
      <hr>
      <div class="form-editar-row">
        <div>
          <label for="editCi">Cédula:</label>
          <input type="text" id="editCi" required>
        </div>
        <div>
          <label for="editExtension">Extensión:</label>
          <input type="text" id="editExtension">
        </div>
      </div>
      <div class="form-editar-row">
        <div>
          <label>Estado civil:</label>
          <div class="radio-group">
            <label><input type="radio" name="editEstcivil" value="soltero"> Soltero/a</label>
            <label><input type="radio" name="editEstcivil" value="casado"> Casado/a</label>
          </div>
        </div>
        <div>
          <label for="editTelefono">Teléfono:</label>
          <input type="text" id="editTelefono">
        </div>
      </div>
      <hr>
      <div>
        <label for="editCorreo">Correo:</label>
        <input type="email" id="editCorreo" required>
      </div>
      <div>
        <label for="editPassword">Contraseña:</label>
        <input type="password" id="editPassword" autocomplete="new-password">
        <small>Déjalo en blanco para no cambiar la contraseña actual. Mínimo 6 caracteres si la cambias.</small>
      </div>

      <div class="form-editar-error" id="editError"></div>
      <div class="form-editar-actions">
        <button type="button" class="btn-cancelar" id="cancelarEditar">Cancelar</button>
        <button type="submit" class="btn-guardar-datos" id="guardarEditar">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: Gestión de cursos -->
<div class="modal-overlay" id="modalCursos">
  <div class="modal">
    <button type="button" class="modal-close" id="cerrarModalCursos">&times;</button>
    <div class="modal-header">
      <div class="modal-kicker">Panel · Cursos</div>
      <h2>Cursos de la plataforma</h2>
      <p>Filtra por área, crea, edita y publica los cursos disponibles.</p>
    </div>

    <div class="cursos-toolbar">
      <div>
        <label for="filtroArea">Área:</label>
        <select class="select-input" id="filtroArea">
          <option value="">Todas las áreas</option>
        </select>
      </div>
      <button type="button" class="btn-nuevo-curso" id="btnNuevoCurso">+ Nuevo curso</button>
    </div>

    <div class="modal-status" id="cursosStatus">Cargando...</div>
    <div class="cursos-list" id="cursosList"></div>
  </div>
</div>

<!-- MODAL: Crear / editar curso -->
<div class="modal-overlay" id="modalEditarCurso">
  <div class="modal">
    <button type="button" class="modal-close" id="cerrarModalEditarCurso">&times;</button>
    <div class="modal-header">
      <div class="modal-kicker">Panel · Cursos</div>
      <h2 id="tituloModalCurso">Nuevo curso</h2>
      <p>Completa los datos del curso. Se crea como borrador hasta que lo publiques.</p>
    </div>
    <form class="form-editar" id="formEditarCurso">
      <input type="hidden" id="cursoId" value="">

      <div>
        <label for="cursoTitulo">Título:</label>
        <input type="text" id="cursoTitulo" required>
      </div>

      <div>
        <label for="cursoDescripcion">Descripción:</label>
        <textarea id="cursoDescripcion"></textarea>
      </div>

      <div class="form-editar-row">
        <div>
          <label for="cursoArea">Área:</label>
          <select id="cursoArea" required>
            <option value="">Selecciona un área</option>
          </select>
        </div>
        <div>
          <label for="cursoDocente">Docente (opcional):</label>
          <select id="cursoDocente">
            <option value="">Sin asignar</option>
          </select>
        </div>
      </div>

      <div class="form-editar-row">
        <div>
          <label for="cursoNivel">Nivel:</label>
          <select id="cursoNivel">
            <option value="basico">Básico</option>
            <option value="intermedio">Intermedio</option>
            <option value="avanzado">Avanzado</option>
          </select>
        </div>
        <div>
          <label for="cursoDuracion">Duración (horas):</label>
          <input type="text" id="cursoDuracion" inputmode="numeric">
        </div>
      </div>

      <div class="form-editar-row">
        <div>
          <label for="cursoPrecio">Precio (Bs.):</label>
          <input type="text" id="cursoPrecio" inputmode="decimal">
        </div>
        <div>
          <label for="cursoImagen">URL de imagen (opcional):</label>
          <input type="text" id="cursoImagen">
        </div>
      </div>

      <div class="form-editar-error" id="editCursoError"></div>
      <div class="form-editar-actions">
        <button type="button" class="btn-cancelar" id="cancelarEditarCurso">Cancelar</button>
        <button type="submit" class="btn-guardar-datos" id="guardarCurso">Guardar curso</button>
      </div>
    </form>
  </div>
</div>

<script>
(function () {
  var TIPOS = { 1: 'Administrador', 2: 'Docente', 3: 'Estudiante' };
  var overlay = document.getElementById('modalUsuarios');
  var lista = document.getElementById('usuariosList');
  var estado = document.getElementById('usuariosStatus');
  var linkUsuarios = document.getElementById('usuarios');
  var btnCerrar = document.getElementById('cerrarModalUsuarios');

  function abrirModal() {
    overlay.classList.add('is-open');
    cargarUsuarios();
  }

  function cerrarModal() {
    overlay.classList.remove('is-open');
  }

  function cargarUsuarios() {
    estado.style.display = 'block';
    estado.textContent = 'Cargando...';
    lista.innerHTML = '';

    fetch('../controllers/UsuariosController.php?action=listar')
      .then(function (res) { return res.json(); })
      .then(function (json) {
        if (!json.ok) {
          estado.textContent = json.error || 'No se pudo cargar la lista.';
          return;
        }
        pintarLista(json.data);
      })
      .catch(function () {
        estado.textContent = 'Error de conexión al cargar usuarios.';
      });
  }

  function pintarLista(personas) {
    if (!personas.length) {
      estado.textContent = 'No hay personas registradas.';
      return;
    }
    estado.style.display = 'none';

    personas.forEach(function (p) {
      var row = document.createElement('div');
      row.className = 'usuario-row';

      var info = document.createElement('div');
      info.className = 'usuario-info';
      info.innerHTML =
        '<div class="nombre">' + escapeHtml(p.nombres + ' ' + p.apellidos) + '</div>' +
        '<div class="correo">' + escapeHtml(p.correo || 'sin correo') + '</div>';

      var select = document.createElement('select');
      [1, 2, 3].forEach(function (val) {
        var opt = document.createElement('option');
        opt.value = val;
        opt.textContent = TIPOS[val];
        if (parseInt(p.tipoper, 10) === val) opt.selected = true;
        select.appendChild(opt);
      });

      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'btn-confirmar';
      btn.textContent = 'Confirmar';

      var btnEstado = document.createElement('button');
      btnEstado.type = 'button';
      pintarBtnEstado(btnEstado, p.estado, p.tipoper);

      btn.addEventListener('click', function () {
        confirmarCambio(p.id, select.value, btn, btnEstado);
      });

      var btnEditar = document.createElement('button');
      btnEditar.type = 'button';
      btnEditar.className = 'btn-modificar';
      btnEditar.textContent = 'Modificar';

      btnEditar.addEventListener('click', function () {
        abrirModalEditar(p.id);
      });

      btnEstado.addEventListener('click', function () {
        var estadoActual = parseInt(btnEstado.dataset.estado, 10);
        var nuevoEstado = estadoActual === 1 ? 0 : 1;
        cambiarEstado(p.id, nuevoEstado, btnEstado);
      });

      row.appendChild(info);
      row.appendChild(select);
      row.appendChild(btn);
      row.appendChild(btnEditar);
      row.appendChild(btnEstado);
      lista.appendChild(row);
    });
  }

  function pintarBtnEstado(btnEstado, estadoPersona, tipoper) {
    var estadoNum = parseInt(estadoPersona, 10) === 0 ? 0 : 1;
    var esAdmin = parseInt(tipoper, 10) === 1;
    btnEstado.dataset.estado = estadoNum;
    btnEstado.dataset.tipoper = parseInt(tipoper, 10);

    if (estadoNum === 1) {
      btnEstado.className = 'btn-estado btn-baja';
      btnEstado.textContent = 'Deshabilitar';
    } else {
      btnEstado.className = 'btn-estado btn-alta';
      btnEstado.textContent = 'Habilitar';
    }

    // Un Administrador no puede ser dado de baja (ni desde aquí ni en el backend).
    if (esAdmin && estadoNum === 1) {
      btnEstado.disabled = true;
      btnEstado.title = 'No se puede dar de baja a un Administrador.';
    } else {
      btnEstado.disabled = false;
      btnEstado.title = '';
    }
  }

  function cambiarEstado(id, nuevoEstado, btnEstado) {
    btnEstado.disabled = true;
    var textoOriginal = btnEstado.textContent;
    btnEstado.textContent = 'Guardando...';

    fetch('../controllers/UsuariosController.php?action=actualizarEstado', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: id, estado: nuevoEstado })
    })
      .then(function (res) { return res.json(); })
      .then(function (json) {
        btnEstado.disabled = false;
        if (json.ok) {
          pintarBtnEstado(btnEstado, nuevoEstado);
        } else {
          btnEstado.textContent = textoOriginal;
        }
      })
      .catch(function () {
        btnEstado.disabled = false;
        btnEstado.textContent = textoOriginal;
      });
  }

  function confirmarCambio(id, tipoper, btn, btnEstado) {
    btn.disabled = true;
    var textoOriginal = btn.textContent;
    btn.textContent = 'Guardando...';

    fetch('../controllers/UsuariosController.php?action=actualizarTipo', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: id, tipoper: tipoper })
    })
      .then(function (res) { return res.json(); })
      .then(function (json) {
        if (json.ok) {
          btn.textContent = 'Guardado ✓';
          btn.classList.add('saved');
          if (btnEstado) {
            pintarBtnEstado(btnEstado, btnEstado.dataset.estado, tipoper);
          }
          setTimeout(function () {
            btn.textContent = textoOriginal;
            btn.classList.remove('saved');
            btn.disabled = false;
          }, 1400);
        } else {
          btn.textContent = 'Error';
          setTimeout(function () {
            btn.textContent = textoOriginal;
            btn.disabled = false;
          }, 1400);
        }
      })
      .catch(function () {
        btn.textContent = 'Error de red';
        setTimeout(function () {
          btn.textContent = textoOriginal;
          btn.disabled = false;
        }, 1400);
      });
  }

  function escapeHtml(str) {
    var div = document.createElement('div');
    div.textContent = str == null ? '' : str;
    return div.innerHTML;
  }

  linkUsuarios.addEventListener('click', function (e) {
    e.preventDefault();
    abrirModal();
  });

  btnCerrar.addEventListener('click', cerrarModal);

  overlay.addEventListener('click', function (e) {
    if (e.target === overlay) cerrarModal();
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && overlay.classList.contains('is-open')) cerrarModal();
  });

  // ---------- Modal: editar datos de una persona ----------
  var overlayEditar = document.getElementById('modalEditarUsuario');
  var formEditar = document.getElementById('formEditarUsuario');
  var editError = document.getElementById('editError');
  var btnGuardarEditar = document.getElementById('guardarEditar');
  var btnCerrarEditar = document.getElementById('cerrarModalEditar');
  var btnCancelarEditar = document.getElementById('cancelarEditar');

  var campoId = document.getElementById('editId');
  var campoNombres = document.getElementById('editNombres');
  var campoApellidos = document.getElementById('editApellidos');
  var campoTelefono = document.getElementById('editTelefono');
  var campoCorreo = document.getElementById('editCorreo');
  var campoPassword = document.getElementById('editPassword');
  var campoFNac = document.getElementById('editFNac');
  var campoCi = document.getElementById('editCi');
  var campoExtension = document.getElementById('editExtension');
  var radiosSexo = document.getElementsByName('editSexo');
  var radiosEstcivil = document.getElementsByName('editEstcivil');

  function marcarRadio(radios, valor) {
    for (var i = 0; i < radios.length; i++) {
      radios[i].checked = (radios[i].value === valor);
    }
  }

  function radioSeleccionado(radios) {
    for (var i = 0; i < radios.length; i++) {
      if (radios[i].checked) return radios[i].value;
    }
    return '';
  }

  function mostrarErrorEditar(msg) {
    editError.textContent = msg;
    editError.style.display = 'block';
  }

  function limpiarErrorEditar() {
    editError.textContent = '';
    editError.style.display = 'none';
  }

  function abrirModalEditar(id) {
    limpiarErrorEditar();
    formEditar.reset();
    campoId.value = id;
    overlayEditar.classList.add('is-open');

    fetch('../controllers/UsuariosController.php?action=obtener&id=' + encodeURIComponent(id))
      .then(function (res) { return res.json(); })
      .then(function (json) {
        if (!json.ok) {
          mostrarErrorEditar(json.error || 'No se pudieron cargar los datos.');
          return;
        }
        campoNombres.value = json.data.nombres || '';
        campoApellidos.value = json.data.apellidos || '';
        campoTelefono.value = json.data.telefono || '';
        campoCorreo.value = json.data.correo || '';
        campoFNac.value = json.data.f_nac || '';
        campoCi.value = json.data.ci || '';
        campoExtension.value = json.data.extension || '';
        marcarRadio(radiosSexo, json.data.sexo || '');
        marcarRadio(radiosEstcivil, json.data.estcivil || '');
      })
      .catch(function () {
        mostrarErrorEditar('Error de conexión al cargar los datos.');
      });
  }

  function cerrarModalEditar() {
    overlayEditar.classList.remove('is-open');
  }

  formEditar.addEventListener('submit', function (e) {
    e.preventDefault();
    limpiarErrorEditar();

    var payload = {
      id: campoId.value,
      nombres: campoNombres.value.trim(),
      apellidos: campoApellidos.value.trim(),
      telefono: campoTelefono.value.trim(),
      correo: campoCorreo.value.trim(),
      password: campoPassword.value,
      f_nac: campoFNac.value,
      ci: campoCi.value.trim(),
      extension: campoExtension.value.trim(),
      sexo: radioSeleccionado(radiosSexo),
      estcivil: radioSeleccionado(radiosEstcivil)
    };

    if (!payload.sexo) {
      mostrarErrorEditar('Selecciona un sexo.');
      return;
    }

    if (!payload.estcivil) {
      mostrarErrorEditar('Selecciona un estado civil.');
      return;
    }

    if (payload.password && payload.password.length < 6) {
      mostrarErrorEditar('La contraseña debe tener al menos 6 caracteres.');
      return;
    }

    btnGuardarEditar.disabled = true;
    btnGuardarEditar.textContent = 'Guardando...';

    fetch('../controllers/UsuariosController.php?action=actualizarDatos', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
      .then(function (res) { return res.json(); })
      .then(function (json) {
        btnGuardarEditar.disabled = false;
        btnGuardarEditar.textContent = 'Guardar cambios';

        if (!json.ok) {
          mostrarErrorEditar(json.error || 'No se pudo guardar los cambios.');
          return;
        }

        cerrarModalEditar();
        cargarUsuarios();
      })
      .catch(function () {
        btnGuardarEditar.disabled = false;
        btnGuardarEditar.textContent = 'Guardar cambios';
        mostrarErrorEditar('Error de conexión al guardar los cambios.');
      });
  });

  btnCerrarEditar.addEventListener('click', cerrarModalEditar);
  btnCancelarEditar.addEventListener('click', cerrarModalEditar);

  overlayEditar.addEventListener('click', function (e) {
    if (e.target === overlayEditar) cerrarModalEditar();
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && overlayEditar.classList.contains('is-open')) cerrarModalEditar();
  });

  // ---------- Módulo: gestión de Cursos ----------
  var NIVELES = { basico: 'Básico', intermedio: 'Intermedio', avanzado: 'Avanzado' };

  var linkCursos = document.getElementById('cursos');
  var overlayCursos = document.getElementById('modalCursos');
  var btnCerrarCursos = document.getElementById('cerrarModalCursos');
  var cursosList = document.getElementById('cursosList');
  var cursosStatus = document.getElementById('cursosStatus');
  var filtroArea = document.getElementById('filtroArea');
  var btnNuevoCurso = document.getElementById('btnNuevoCurso');

  var overlayEditarCurso = document.getElementById('modalEditarCurso');
  var formEditarCurso = document.getElementById('formEditarCurso');
  var editCursoError = document.getElementById('editCursoError');
  var btnCerrarEditarCurso = document.getElementById('cerrarModalEditarCurso');
  var btnCancelarEditarCurso = document.getElementById('cancelarEditarCurso');
  var btnGuardarCurso = document.getElementById('guardarCurso');
  var tituloModalCurso = document.getElementById('tituloModalCurso');

  var campoCursoId = document.getElementById('cursoId');
  var campoCursoTitulo = document.getElementById('cursoTitulo');
  var campoCursoDescripcion = document.getElementById('cursoDescripcion');
  var campoCursoArea = document.getElementById('cursoArea');
  var campoCursoDocente = document.getElementById('cursoDocente');
  var campoCursoNivel = document.getElementById('cursoNivel');
  var campoCursoDuracion = document.getElementById('cursoDuracion');
  var campoCursoPrecio = document.getElementById('cursoPrecio');
  var campoCursoImagen = document.getElementById('cursoImagen');

  var areasCache = [];

  function abrirModalCursos() {
    overlayCursos.classList.add('is-open');
    cargarAreasFiltro().then(cargarCursos);
  }

  function cerrarModalCursos() {
    overlayCursos.classList.remove('is-open');
  }

  function cargarAreasFiltro() {
    return fetch('../controllers/CursosController.php?action=listarAreas')
      .then(function (res) { return res.json(); })
      .then(function (json) {
        if (!json.ok) return;
        areasCache = json.data;

        filtroArea.innerHTML = '<option value="">Todas las áreas</option>';
        campoCursoArea.innerHTML = '<option value="">Selecciona un área</option>';

        areasCache.forEach(function (a) {
          var opt1 = document.createElement('option');
          opt1.value = a.id;
          opt1.textContent = a.nombre;
          filtroArea.appendChild(opt1);

          var opt2 = document.createElement('option');
          opt2.value = a.id;
          opt2.textContent = a.nombre;
          campoCursoArea.appendChild(opt2);
        });
      })
      .catch(function () {});
  }

  function cargarDocentesSelect() {
    return fetch('../controllers/CursosController.php?action=listarDocentes')
      .then(function (res) { return res.json(); })
      .then(function (json) {
        if (!json.ok) return;
        campoCursoDocente.innerHTML = '<option value="">Sin asignar</option>';
        json.data.forEach(function (d) {
          var opt = document.createElement('option');
          opt.value = d.id;
          opt.textContent = d.nombres + ' ' + d.apellidos;
          campoCursoDocente.appendChild(opt);
        });
      })
      .catch(function () {});
  }

  function cargarCursos() {
    cursosStatus.style.display = 'block';
    cursosStatus.textContent = 'Cargando...';
    cursosList.innerHTML = '';

    var url = '../controllers/CursosController.php?action=listar';
    if (filtroArea.value) url += '&area_id=' + encodeURIComponent(filtroArea.value);

    fetch(url)
      .then(function (res) { return res.json(); })
      .then(function (json) {
        if (!json.ok) {
          cursosStatus.textContent = json.error || 'No se pudo cargar la lista.';
          return;
        }
        pintarCursos(json.data);
      })
      .catch(function () {
        cursosStatus.textContent = 'Error de conexión al cargar cursos.';
      });
  }

  function pintarCursos(cursos) {
    if (!cursos.length) {
      cursosStatus.style.display = 'block';
      cursosStatus.textContent = 'No hay cursos para este filtro.';
      return;
    }
    cursosStatus.style.display = 'none';

    cursos.forEach(function (c) {
      var row = document.createElement('div');
      row.className = 'curso-row';

      var info = document.createElement('div');
      info.className = 'curso-info';

      var precioTxt = 'Bs. ' + parseFloat(c.precio || 0).toFixed(2);
      var duracionTxt = c.duracion_horas ? c.duracion_horas + ' h' : 'Duración N/D';
      var docenteTxt = c.docente_nombres
        ? escapeHtml(c.docente_nombres + ' ' + c.docente_apellidos)
        : 'Sin docente asignado';

      var metaHtml =
        '<span class="badge badge-area">' + escapeHtml(c.area_nombre) + '</span>' +
        '<span class="badge badge-nivel">' + escapeHtml(NIVELES[c.nivel] || c.nivel) + '</span>' +
        (parseInt(c.estado, 10) === 0 ? '<span class="badge badge-inactivo">Inactivo</span>' : '') +
        '<span>' + precioTxt + ' · ' + duracionTxt + '</span>';

      info.innerHTML =
        '<div class="titulo">' + escapeHtml(c.titulo) + '</div>' +
        '<div class="meta">' + metaHtml + '</div>' +
        '<div class="meta">' + docenteTxt + '</div>';

      var btnPublicar = document.createElement('button');
      btnPublicar.type = 'button';
      pintarBtnPublicar(btnPublicar, c.publicado);
      btnPublicar.addEventListener('click', function () {
        var nuevo = parseInt(btnPublicar.dataset.publicado, 10) === 1 ? 0 : 1;
        cambiarPublicacion(c.id, nuevo, btnPublicar);
      });

      var btnEditar = document.createElement('button');
      btnEditar.type = 'button';
      btnEditar.className = 'btn-modificar';
      btnEditar.textContent = 'Editar';
      btnEditar.addEventListener('click', function () {
        abrirModalEditarCurso(c.id);
      });

      var btnEstado = document.createElement('button');
      btnEstado.type = 'button';
      pintarBtnEstadoCurso(btnEstado, c.estado);
      btnEstado.addEventListener('click', function () {
        var nuevo = parseInt(btnEstado.dataset.estado, 10) === 1 ? 0 : 1;
        cambiarEstadoCurso(c.id, nuevo, btnEstado, row, c);
      });

      row.appendChild(info);
      row.appendChild(btnPublicar);
      row.appendChild(btnEditar);
      row.appendChild(btnEstado);
      cursosList.appendChild(row);
    });
  }

  function pintarBtnPublicar(btn, publicado) {
    var estaPublicado = parseInt(publicado, 10) === 1;
    btn.dataset.publicado = estaPublicado ? 1 : 0;
    btn.className = 'btn-publicar ' + (estaPublicado ? 'pub-si' : 'pub-no');
    btn.textContent = estaPublicado ? 'Publicado' : 'Publicar';
  }

  function pintarBtnEstadoCurso(btn, estado) {
    var activo = parseInt(estado, 10) !== 0;
    btn.dataset.estado = activo ? 1 : 0;
    btn.className = activo ? 'btn-estado btn-baja' : 'btn-estado btn-alta';
    btn.textContent = activo ? 'Dar de baja' : 'Dar de alta';
  }

  function cambiarPublicacion(id, nuevoPublicado, btn) {
    btn.disabled = true;
    var textoOriginal = btn.textContent;
    btn.textContent = 'Guardando...';

    fetch('../controllers/CursosController.php?action=actualizarPublicacion', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: id, publicado: nuevoPublicado })
    })
      .then(function (res) { return res.json(); })
      .then(function (json) {
        btn.disabled = false;
        if (json.ok) {
          pintarBtnPublicar(btn, nuevoPublicado);
        } else {
          btn.textContent = textoOriginal;
        }
      })
      .catch(function () {
        btn.disabled = false;
        btn.textContent = textoOriginal;
      });
  }

  function cambiarEstadoCurso(id, nuevoEstado, btn) {
    btn.disabled = true;
    var textoOriginal = btn.textContent;
    btn.textContent = 'Guardando...';

    fetch('../controllers/CursosController.php?action=actualizarEstado', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: id, estado: nuevoEstado })
    })
      .then(function (res) { return res.json(); })
      .then(function (json) {
        btn.disabled = false;
        if (json.ok) {
          pintarBtnEstadoCurso(btn, nuevoEstado);
          cargarCursos();
        } else {
          btn.textContent = textoOriginal;
        }
      })
      .catch(function () {
        btn.disabled = false;
        btn.textContent = textoOriginal;
      });
  }

  function mostrarErrorCurso(msg) {
    editCursoError.textContent = msg;
    editCursoError.style.display = 'block';
  }

  function limpiarErrorCurso() {
    editCursoError.textContent = '';
    editCursoError.style.display = 'none';
  }

  function abrirModalEditarCurso(id) {
    limpiarErrorCurso();
    formEditarCurso.reset();
    campoCursoId.value = id || '';
    tituloModalCurso.textContent = id ? 'Editar curso' : 'Nuevo curso';
    overlayEditarCurso.classList.add('is-open');

    cargarDocentesSelect().then(function () {
      if (!id) return;

      fetch('../controllers/CursosController.php?action=obtener&id=' + encodeURIComponent(id))
        .then(function (res) { return res.json(); })
        .then(function (json) {
          if (!json.ok) {
            mostrarErrorCurso(json.error || 'No se pudieron cargar los datos del curso.');
            return;
          }
          var d = json.data;
          campoCursoTitulo.value = d.titulo || '';
          campoCursoDescripcion.value = d.descripcion || '';
          campoCursoArea.value = d.area_id || '';
          campoCursoDocente.value = d.docente_id || '';
          campoCursoNivel.value = d.nivel || 'basico';
          campoCursoDuracion.value = d.duracion_horas || '';
          campoCursoPrecio.value = d.precio || '';
          campoCursoImagen.value = d.imagen_url || '';
        })
        .catch(function () {
          mostrarErrorCurso('Error de conexión al cargar los datos del curso.');
        });
    });
  }

  function cerrarModalEditarCurso() {
    overlayEditarCurso.classList.remove('is-open');
  }

  formEditarCurso.addEventListener('submit', function (e) {
    e.preventDefault();
    limpiarErrorCurso();

    var id = campoCursoId.value;
    var payload = {
      titulo: campoCursoTitulo.value.trim(),
      descripcion: campoCursoDescripcion.value.trim(),
      area_id: campoCursoArea.value,
      docente_id: campoCursoDocente.value,
      nivel: campoCursoNivel.value,
      duracion_horas: campoCursoDuracion.value.trim(),
      precio: campoCursoPrecio.value.trim(),
      imagen_url: campoCursoImagen.value.trim()
    };

    if (!payload.titulo) {
      mostrarErrorCurso('El título es obligatorio.');
      return;
    }
    if (!payload.area_id) {
      mostrarErrorCurso('Selecciona un área.');
      return;
    }

    var accion = id ? 'actualizar' : 'crear';
    if (id) payload.id = id;

    btnGuardarCurso.disabled = true;
    btnGuardarCurso.textContent = 'Guardando...';

    fetch('../controllers/CursosController.php?action=' + accion, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
      .then(function (res) { return res.json(); })
      .then(function (json) {
        btnGuardarCurso.disabled = false;
        btnGuardarCurso.textContent = 'Guardar curso';

        if (!json.ok) {
          mostrarErrorCurso(json.error || 'No se pudo guardar el curso.');
          return;
        }

        cerrarModalEditarCurso();
        cargarCursos();
      })
      .catch(function () {
        btnGuardarCurso.disabled = false;
        btnGuardarCurso.textContent = 'Guardar curso';
        mostrarErrorCurso('Error de conexión al guardar el curso.');
      });
  });

  linkCursos.addEventListener('click', function (e) {
    e.preventDefault();
    abrirModalCursos();
  });

  btnCerrarCursos.addEventListener('click', cerrarModalCursos);
  overlayCursos.addEventListener('click', function (e) {
    if (e.target === overlayCursos) cerrarModalCursos();
  });

  filtroArea.addEventListener('change', cargarCursos);

  btnNuevoCurso.addEventListener('click', function () {
    abrirModalEditarCurso(null);
  });

  btnCerrarEditarCurso.addEventListener('click', cerrarModalEditarCurso);
  btnCancelarEditarCurso.addEventListener('click', cerrarModalEditarCurso);
  overlayEditarCurso.addEventListener('click', function (e) {
    if (e.target === overlayEditarCurso) cerrarModalEditarCurso();
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      if (overlayEditarCurso.classList.contains('is-open')) cerrarModalEditarCurso();
      else if (overlayCursos.classList.contains('is-open')) cerrarModalCursos();
    }
  });
})();
</script>
</body>
</html>