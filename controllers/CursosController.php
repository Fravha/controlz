<?php
/**
 * CursosController.php
 * Endpoints para el módulo de Cursos (crear, editar, publicar, listar por área).
 *
 * IMPORTANTE — AJUSTA ESTO A TU PROYECTO:
 * Este archivo asume una clase Database con un método estático getConnection()
 * que devuelve un PDO, ubicada en ../config/Database.php (mismo lugar donde
 * probablemente vive la conexión que usa UsuariosController.php).
 * Si tu conexión se llama distinto (por ejemplo conexion.php con una función
 * conectar()), reemplaza únicamente el bloque "CONEXIÓN A LA BASE DE DATOS"
 * de más abajo — el resto del archivo no depende de esa implementación.
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

// ---------------------------------------------------------------------
// Protección: solo Administrador (tipoper = 1), igual que index.php
// ---------------------------------------------------------------------
if (!isset($_SESSION['persona_id']) || (int) $_SESSION['persona_tipoper'] !== 1) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'No autorizado.']);
    exit;
}

// ---------------------------------------------------------------------
// CONEXIÓN A LA BASE DE DATOS
// ---------------------------------------------------------------------
require_once __DIR__ . '/../config/Database.php';

try {
    $pdo = Database::getConnection();
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No se pudo conectar a la base de datos.']);
    exit;
}

$action = $_GET['action'] ?? '';

function leerJson()
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function validarNivel($nivel)
{
    return in_array($nivel, ['basico', 'intermedio', 'avanzado'], true);
}

switch ($action) {

    // -------------------------------------------------------------
    // GET: listar áreas (para el <select> del filtro y del formulario)
    // -------------------------------------------------------------
    case 'listarAreas':
        try {
            $stmt = $pdo->query(
                "SELECT id, nombre, descripcion, estado
                 FROM areas
                 WHERE estado = 1
                 ORDER BY nombre ASC"
            );
            echo json_encode(['ok' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'No se pudieron cargar las áreas.']);
        }
        break;

    // -------------------------------------------------------------
    // GET: listar docentes activos (tipoper = 2) para el <select> del formulario
    // -------------------------------------------------------------
    case 'listarDocentes':
        try {
            $stmt = $pdo->query(
                "SELECT id, nombres, apellidos
                 FROM personas
                 WHERE tipoper = 2 AND estado = 1
                 ORDER BY nombres ASC"
            );
            echo json_encode(['ok' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'No se pudieron cargar los docentes.']);
        }
        break;

    // -------------------------------------------------------------
    // GET: listar cursos, opcionalmente filtrados por área
    //      ?action=listar&area_id=3
    // -------------------------------------------------------------
    case 'listar':
        try {
            $areaId = isset($_GET['area_id']) && $_GET['area_id'] !== ''
                ? (int) $_GET['area_id']
                : null;

            $sql = "SELECT c.id, c.area_id, c.docente_id, c.titulo, c.descripcion,
                           c.nivel, c.precio, c.duracion_horas, c.imagen_url,
                           c.publicado, c.estado,
                           a.nombre AS area_nombre,
                           p.nombres AS docente_nombres, p.apellidos AS docente_apellidos
                    FROM cursos c
                    INNER JOIN areas a ON a.id = c.area_id
                    LEFT JOIN personas p ON p.id = c.docente_id";

            $params = [];
            if ($areaId !== null) {
                $sql .= " WHERE c.area_id = :area_id";
                $params[':area_id'] = $areaId;
            }
            $sql .= " ORDER BY c.created_at DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            echo json_encode(['ok' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'No se pudo cargar la lista de cursos.']);
        }
        break;

    // -------------------------------------------------------------
    // GET: obtener un curso puntual (para precargar el modal de edición)
    //      ?action=obtener&id=5
    // -------------------------------------------------------------
    case 'obtener':
        try {
            $id = (int) ($_GET['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['ok' => false, 'error' => 'Id de curso inválido.']);
                break;
            }
            $stmt = $pdo->prepare(
                "SELECT id, area_id, docente_id, titulo, descripcion, nivel,
                        precio, duracion_horas, imagen_url, publicado, estado
                 FROM cursos WHERE id = :id LIMIT 1"
            );
            $stmt->execute([':id' => $id]);
            $curso = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$curso) {
                echo json_encode(['ok' => false, 'error' => 'Curso no encontrado.']);
                break;
            }
            echo json_encode(['ok' => true, 'data' => $curso]);
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'No se pudo cargar el curso.']);
        }
        break;

    // -------------------------------------------------------------
    // POST: crear un curso nuevo (se crea como borrador, publicado = 0)
    // -------------------------------------------------------------
    case 'crear':
        try {
            $body = leerJson();

            $titulo   = trim($body['titulo'] ?? '');
            $areaId   = (int) ($body['area_id'] ?? 0);
            $nivel    = $body['nivel'] ?? 'basico';
            $precio   = isset($body['precio']) && $body['precio'] !== '' ? (float) $body['precio'] : 0;
            $duracion = isset($body['duracion_horas']) && $body['duracion_horas'] !== ''
                ? (int) $body['duracion_horas'] : null;
            $docenteId = isset($body['docente_id']) && $body['docente_id'] !== ''
                ? (int) $body['docente_id'] : null;
            $descripcion = trim($body['descripcion'] ?? '');
            $imagenUrl   = trim($body['imagen_url'] ?? '');

            if ($titulo === '') {
                echo json_encode(['ok' => false, 'error' => 'El título es obligatorio.']);
                break;
            }
            if ($areaId <= 0) {
                echo json_encode(['ok' => false, 'error' => 'Selecciona un área.']);
                break;
            }
            if (!validarNivel($nivel)) {
                echo json_encode(['ok' => false, 'error' => 'Nivel inválido.']);
                break;
            }
            if ($precio < 0) {
                echo json_encode(['ok' => false, 'error' => 'El precio no puede ser negativo.']);
                break;
            }

            $stmt = $pdo->prepare(
                "INSERT INTO cursos
                    (area_id, docente_id, titulo, descripcion, nivel, precio,
                     duracion_horas, imagen_url, publicado, estado)
                 VALUES
                    (:area_id, :docente_id, :titulo, :descripcion, :nivel, :precio,
                     :duracion_horas, :imagen_url, 0, 1)"
            );
            $stmt->execute([
                ':area_id'        => $areaId,
                ':docente_id'     => $docenteId,
                ':titulo'         => $titulo,
                ':descripcion'    => $descripcion,
                ':nivel'          => $nivel,
                ':precio'         => $precio,
                ':duracion_horas' => $duracion,
                ':imagen_url'     => $imagenUrl !== '' ? $imagenUrl : null,
            ]);

            echo json_encode(['ok' => true, 'id' => (int) $pdo->lastInsertId()]);
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'No se pudo crear el curso.']);
        }
        break;

    // -------------------------------------------------------------
    // POST: actualizar los datos de un curso existente
    // -------------------------------------------------------------
    case 'actualizar':
        try {
            $body = leerJson();
            $id = (int) ($body['id'] ?? 0);

            $titulo   = trim($body['titulo'] ?? '');
            $areaId   = (int) ($body['area_id'] ?? 0);
            $nivel    = $body['nivel'] ?? 'basico';
            $precio   = isset($body['precio']) && $body['precio'] !== '' ? (float) $body['precio'] : 0;
            $duracion = isset($body['duracion_horas']) && $body['duracion_horas'] !== ''
                ? (int) $body['duracion_horas'] : null;
            $docenteId = isset($body['docente_id']) && $body['docente_id'] !== ''
                ? (int) $body['docente_id'] : null;
            $descripcion = trim($body['descripcion'] ?? '');
            $imagenUrl   = trim($body['imagen_url'] ?? '');

            if ($id <= 0) {
                echo json_encode(['ok' => false, 'error' => 'Id de curso inválido.']);
                break;
            }
            if ($titulo === '') {
                echo json_encode(['ok' => false, 'error' => 'El título es obligatorio.']);
                break;
            }
            if ($areaId <= 0) {
                echo json_encode(['ok' => false, 'error' => 'Selecciona un área.']);
                break;
            }
            if (!validarNivel($nivel)) {
                echo json_encode(['ok' => false, 'error' => 'Nivel inválido.']);
                break;
            }
            if ($precio < 0) {
                echo json_encode(['ok' => false, 'error' => 'El precio no puede ser negativo.']);
                break;
            }

            $stmt = $pdo->prepare(
                "UPDATE cursos SET
                    area_id = :area_id,
                    docente_id = :docente_id,
                    titulo = :titulo,
                    descripcion = :descripcion,
                    nivel = :nivel,
                    precio = :precio,
                    duracion_horas = :duracion_horas,
                    imagen_url = :imagen_url
                 WHERE id = :id"
            );
            $stmt->execute([
                ':area_id'        => $areaId,
                ':docente_id'     => $docenteId,
                ':titulo'         => $titulo,
                ':descripcion'    => $descripcion,
                ':nivel'          => $nivel,
                ':precio'         => $precio,
                ':duracion_horas' => $duracion,
                ':imagen_url'     => $imagenUrl !== '' ? $imagenUrl : null,
                ':id'             => $id,
            ]);

            echo json_encode(['ok' => true]);
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'No se pudo guardar los cambios.']);
        }
        break;

    // -------------------------------------------------------------
    // POST: publicar / despublicar un curso
    //       body: { id, publicado: 0|1 }
    // -------------------------------------------------------------
    case 'actualizarPublicacion':
        try {
            $body = leerJson();
            $id = (int) ($body['id'] ?? 0);
            $publicado = isset($body['publicado']) ? (int) $body['publicado'] : null;

            if ($id <= 0 || !in_array($publicado, [0, 1], true)) {
                echo json_encode(['ok' => false, 'error' => 'Datos inválidos.']);
                break;
            }

            $stmt = $pdo->prepare("UPDATE cursos SET publicado = :publicado WHERE id = :id");
            $stmt->execute([':publicado' => $publicado, ':id' => $id]);

            echo json_encode(['ok' => true]);
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'No se pudo actualizar la publicación.']);
        }
        break;

    // -------------------------------------------------------------
    // POST: dar de alta / baja un curso (soft delete)
    //       body: { id, estado: 0|1 }
    // -------------------------------------------------------------
    case 'actualizarEstado':
        try {
            $body = leerJson();
            $id = (int) ($body['id'] ?? 0);
            $estado = isset($body['estado']) ? (int) $body['estado'] : null;

            if ($id <= 0 || !in_array($estado, [0, 1], true)) {
                echo json_encode(['ok' => false, 'error' => 'Datos inválidos.']);
                break;
            }

            $stmt = $pdo->prepare("UPDATE cursos SET estado = :estado WHERE id = :id");
            $stmt->execute([':estado' => $estado, ':id' => $id]);

            echo json_encode(['ok' => true]);
        } catch (Throwable $e) {
            echo json_encode(['ok' => false, 'error' => 'No se pudo actualizar el estado.']);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['ok' => false, 'error' => 'Acción no encontrada.']);
        break;
}
