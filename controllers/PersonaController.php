<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../models/Persona.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, 'Método no permitido.', null, 405);
}
$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    switch ($action) {
        case 'registrar':
            registrarPersona();
            break;

        default:
            responder(false, 'Acción no válida.', null, 400);
    }
} catch (PDOException $e) {
    // En producción no mostramos el detalle SQL al usuario.
    error_log('CTRL Z PDO: ' . $e->getMessage());
    responder(false, 'No se pudo conectar o guardar la información en la base de datos.', null, 500);
} catch (Exception $e) {
    error_log('CTRL Z: ' . $e->getMessage());
    responder(false, 'Ocurrió un error inesperado.', null, 500);
}

function registrarPersona()
{
    $nombres   = limpiar(isset($_POST['nombres']) ? $_POST['nombres'] : '');
    $apellidos = limpiar(isset($_POST['apellidos']) ? $_POST['apellidos'] : '');
    $telefono  = limpiar(isset($_POST['telefono']) ? $_POST['telefono'] : '');
    $correo    = strtolower(trim(isset($_POST['correo']) ? $_POST['correo'] : ''));
    $password  = (string) (isset($_POST['password']) ? $_POST['password'] : '');
    $ci        = limpiar(isset($_POST['ci']) ? $_POST['ci'] : '');
    $extension = limpiar(isset($_POST['extension']) ? $_POST['extension'] : '');
    $fNac      = trim(isset($_POST['f_nac']) ? $_POST['f_nac'] : '');
    $sexo      = limpiar(isset($_POST['sexo']) ? $_POST['sexo'] : '');
    $estcivil  = limpiar(isset($_POST['estcivil']) ? $_POST['estcivil'] : '');
    $tipoper   = isset($_POST['tipoper']) ? (int) $_POST['tipoper'] : 0;

    if ($nombres === '' || $apellidos === '' || $correo === '' || $password === '' || $ci === '' || $fNac === '' || $sexo === '' || $estcivil === '') {
        responder(false, 'Completa todos los campos obligatorios.', null, 422);
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        responder(false, 'El correo electrónico no es válido.', null, 422);
    }

    if (strlen($password) < 6) {
        responder(false, 'La contraseña debe tener al menos 6 caracteres.', null, 422);
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fNac)) {
        responder(false, 'La fecha de nacimiento no tiene un formato válido.', null, 422);
    }

    $fecha = DateTime::createFromFormat('Y-m-d', $fNac);
    if (!$fecha || $fecha->format('Y-m-d') !== $fNac) {
        responder(false, 'La fecha de nacimiento no es válida.', null, 422);
    }

    if (!in_array($sexo, array('M', 'F'), true)) {
        responder(false, 'Selecciona un sexo válido.', null, 422);
    }

    if (!in_array($estcivil, array('soltero', 'casado'), true)) {
        responder(false, 'Selecciona un estado civil válido.', null, 422);
    }

    if (!in_array($tipoper, array(1, 2, 3), true)) {
        responder(false, 'Selecciona un tipo de persona válido.', null, 422);
    }

    $persona = new Persona();

    if ($persona->existe($ci, $correo)) {
        responder(false, 'La cédula o el correo ya están registrados.', null, 409);
    }

    $id = $persona->registrar(array(
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
        'tipoper'   => $tipoper,
    ));

    responder(true, 'Registro realizado correctamente. Bienvenido/a.', array('id' => $id), 201);
}

function limpiar($valor)
{
    return trim($valor);
}

function responder($ok, $mensaje, $data = null, $status = 200)
{
    http_response_code($status);
    echo json_encode(array(
        'ok' => $ok,
        'mensaje' => $mensaje,
        'data' => $data,
    ), JSON_UNESCAPED_UNICODE);
    exit;
}
