<?php
session_start();

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../models/Persona.php';

// Solo el administrador (tipoper = 1) puede listar o modificar tipos de persona.
if (!isset($_SESSION['persona_id']) || (int) $_SESSION['persona_tipoper'] !== 1) {
    http_response_code(403);
    echo json_encode(array('ok' => false, 'error' => 'No autorizado'));
    exit;
}

$persona = new Persona();
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {

    case 'listar':
        try {
            $personas = $persona->listarTodos();
            echo json_encode(array('ok' => true, 'data' => $personas));
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array('ok' => false, 'error' => 'No se pudo obtener la lista'));
        }
        break;

    case 'actualizarTipo':
        $input = json_decode(file_get_contents('php://input'), true);

        $id = isset($input['id']) ? (int) $input['id'] : 0;
        $tipoper = isset($input['tipoper']) ? (int) $input['tipoper'] : 0;

        if ($id <= 0 || !in_array($tipoper, array(1, 2, 3), true)) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'Datos inválidos'));
            exit;
        }

        try {
            $actualizado = $persona->actualizarTipoPer($id, $tipoper);
            echo json_encode(array('ok' => true, 'actualizado' => $actualizado));
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array('ok' => false, 'error' => 'No se pudo actualizar'));
        }
        break;

    case 'obtener':
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'Id inválido'));
            exit;
        }

        try {
            $datos = $persona->buscarPorId($id);

            if (!$datos) {
                http_response_code(404);
                echo json_encode(array('ok' => false, 'error' => 'Persona no encontrada'));
                exit;
            }

            echo json_encode(array('ok' => true, 'data' => $datos));
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array('ok' => false, 'error' => 'No se pudo obtener la persona'));
        }
        break;

    case 'actualizarDatos':
        $input = json_decode(file_get_contents('php://input'), true);

        $id        = isset($input['id']) ? (int) $input['id'] : 0;
        $nombres   = trim(isset($input['nombres']) ? $input['nombres'] : '');
        $apellidos = trim(isset($input['apellidos']) ? $input['apellidos'] : '');
        $telefono  = trim(isset($input['telefono']) ? $input['telefono'] : '');
        $correo    = strtolower(trim(isset($input['correo']) ? $input['correo'] : ''));
        $password  = isset($input['password']) ? (string) $input['password'] : '';
        $ci        = trim(isset($input['ci']) ? $input['ci'] : '');
        $extension = trim(isset($input['extension']) ? $input['extension'] : '');
        $fNac      = trim(isset($input['f_nac']) ? $input['f_nac'] : '');
        $sexo      = trim(isset($input['sexo']) ? $input['sexo'] : '');
        $estcivil  = trim(isset($input['estcivil']) ? $input['estcivil'] : '');

        if ($id <= 0 || $nombres === '' || $apellidos === '' || $ci === '' || $fNac === ''
            || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'Completa todos los campos obligatorios.'));
            exit;
        }

        if ($password !== '' && strlen($password) < 6) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'La contraseña debe tener al menos 6 caracteres.'));
            exit;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fNac)) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'La fecha de nacimiento no tiene un formato válido.'));
            exit;
        }

        $fecha = DateTime::createFromFormat('Y-m-d', $fNac);
        if (!$fecha || $fecha->format('Y-m-d') !== $fNac) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'La fecha de nacimiento no es válida.'));
            exit;
        }

        if (!in_array($sexo, array('M', 'F'), true)) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'Selecciona un sexo válido.'));
            exit;
        }

        if (!in_array($estcivil, array('soltero', 'casado'), true)) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'Selecciona un estado civil válido.'));
            exit;
        }

        try {
            if ($persona->correoExiste($correo, $id)) {
                http_response_code(409);
                echo json_encode(array('ok' => false, 'error' => 'Ese correo ya está en uso por otra persona.'));
                exit;
            }

            if ($persona->ciExiste($ci, $id)) {
                http_response_code(409);
                echo json_encode(array('ok' => false, 'error' => 'Esa cédula ya está en uso por otra persona.'));
                exit;
            }

            $actualizado = $persona->actualizarDatos($id, array(
                'nombres'   => $nombres,
                'apellidos' => $apellidos,
                'telefono'  => $telefono,
                'correo'    => $correo,
                'password'  => $password,
                'ci'        => $ci,
                'extension' => $extension,
                'f_nac'     => $fNac,
                'sexo'      => $sexo,
                'estcivil'  => $estcivil,
            ));

            echo json_encode(array('ok' => true, 'actualizado' => $actualizado));
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array('ok' => false, 'error' => 'No se pudo actualizar los datos'));
        }
        break;

    case 'actualizarEstado':
        $input = json_decode(file_get_contents('php://input'), true);

        $id = isset($input['id']) ? (int) $input['id'] : 0;
        $nuevoEstado = isset($input['estado']) ? (int) $input['estado'] : -1;

        if ($id <= 0 || !in_array($nuevoEstado, array(0, 1), true)) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'Datos inválidos'));
            exit;
        }

        // Evita que el administrador se dé de baja a sí mismo y quede
        // bloqueado fuera del panel.
        if ($id === (int) $_SESSION['persona_id'] && $nuevoEstado === 0) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'No puedes dar de baja tu propia cuenta.'));
            exit;
        }

        try {
            // Ningún Administrador puede darse de baja, ni siquiera por
            // otro administrador: se protege el rol completo, no solo
            // la sesión activa.
            if ($nuevoEstado === 0) {
                $datosPersona = $persona->buscarPorId($id);
                if ($datosPersona && (int) $datosPersona['tipoper'] === 1) {
                    http_response_code(400);
                    echo json_encode(array('ok' => false, 'error' => 'No se puede dar de baja a un Administrador.'));
                    exit;
                }
            }

            $actualizado = $persona->actualizarEstado($id, $nuevoEstado);
            echo json_encode(array('ok' => true, 'actualizado' => $actualizado));
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array('ok' => false, 'error' => 'No se pudo actualizar el estado'));
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(array('ok' => false, 'error' => 'Acción no reconocida'));
}
