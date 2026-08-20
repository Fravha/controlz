<?php

session_start();

$action = isset($_GET['action']) ? $_GET['action'] : '';

// El logout no requiere JSON ni método POST: es un enlace normal.
if ($action === 'logout') {
    $_SESSION = array();
    session_destroy();
    header('Location: ../index.html');
    exit;
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../models/Persona.php';
require_once __DIR__ . '/../libs/PHPMailer/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, 'Método no permitido.', null, 405);
}

try {
    switch ($action) {
        case 'login':
            loginPersona();
            break;

        case 'verificar-codigo':
            verificarCodigo();
            break;

        case 'reenviar-codigo':
            reenviarCodigo();
            break;

        default:
            responder(false, 'Acción no válida.', null, 400);
    }
} catch (PDOException $e) {
    error_log('CTRL Z PDO: ' . $e->getMessage());
    responder(false, 'No se pudo conectar con la base de datos.', null, 500);
} catch (Exception $e) {
    error_log('CTRL Z: ' . $e->getMessage());
    responder(false, 'Ocurrió un error inesperado.', null, 500);
}

/**
 * Valida credenciales contra la tabla persona y, si corresponde,
 * abre sesión y devuelve a qué página redirigir según tipoper.
 */
function loginPersona()
{
    $correo   = strtolower(trim(isset($_POST['correo']) ? $_POST['correo'] : ''));
    $password = (string) (isset($_POST['password']) ? $_POST['password'] : '');

    if ($correo === '' || $password === '') {
        responder(false, 'Ingresa tu correo y contraseña.', null, 422);
    }

    $persona = new Persona();
    $usuario = $persona->buscarPorCorreo($correo);

    // Mensaje genérico para no revelar si el correo existe o no.
    if (!$usuario) {
        responder(false, 'Usuario o contraseña incorrectos.', null, 401);
    }

    if ((int) $usuario['estado'] === 0) {
        responder(false, 'Tu cuenta está desactivada. Consulte al Administrador.', null, 403);
    }

    if (!password_verify($password, $usuario['contrase'])) {
        // Contraseña incorrecta: se registra el intento en loginerror.
        $persona->registrarErrorLogin($usuario['id_usuario']);
        $intentos = $persona->contarErroresLogin($usuario['id_usuario']);

        if ($intentos >= 3) {
            // 3er intento fallido: se desactiva persona y usuario.
            $persona->actualizarEstado($usuario['id'], 0);
            responder(false, 'Tu cuenta ha sido desactivada por 3 intentos fallidos. Consulte al Administrador.', null, 403);
        }

        $restantes = 3 - $intentos;
        responder(false, "Usuario o contraseña incorrectos. Te queda(n) {$restantes} intento(s).", null, 401);
    }

    // Login correcto: se limpia el historial de intentos fallidos.
    $persona->limpiarErroresLogin($usuario['id_usuario']);

    $tipoper = (int) $usuario['tipoper'];

    if (!in_array($tipoper, array(1, 2, 3), true)) {
        responder(false, 'Tu cuenta no tiene un tipo de acceso válido asignado.', null, 403);
    }

    // Contraseña correcta: en vez de abrir sesión directo, se pide
    // el segundo factor (código de 4 dígitos enviado por correo).
    session_regenerate_id(true);

    // Datos "pendientes": el usuario todavía NO está autenticado.
    // Se confirman recién cuando verificarCodigo() valida el código.
    $_SESSION['pendiente_id']        = (int) $usuario['id'];
    $_SESSION['pendiente_nombres']   = $usuario['nombres'];
    $_SESSION['pendiente_apellidos'] = $usuario['apellidos'];
    $_SESSION['pendiente_correo']    = $usuario['correo'];
    $_SESSION['pendiente_tipoper']   = $tipoper;

    enviarCodigoAlCorreo($persona, $usuario['correo'], $usuario['nombres']);

    responder(true, 'Te enviamos un código de verificación a tu correo.', array(
        'requiere_verificacion' => true,
        'correo' => $usuario['correo'],
    ), 200);
}

/**
 * Genera un código de 4 dígitos, lo guarda en BD y lo envía
 * por correo con PHPMailer.
 */
function enviarCodigoAlCorreo($persona, $correo, $nombres)
{
    $codigo = str_pad((string) generarNumeroAleatorio4Digitos(), 4, '0', STR_PAD_LEFT);
    $expiraEn = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    $persona->invalidarCodigosAnteriores($correo);
    $persona->guardarCodigoVerificacion($correo, $codigo, $expiraEn);

    $smtp = require __DIR__ . '/../config/smtp.php';

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $smtp['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp['usuario'];
        $mail->Password   = $smtp['password'];
        $mail->SMTPSecure = $smtp['encriptado'];
        $mail->Port       = $smtp['puerto'];
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($smtp['usuario'], $smtp['remitente_nombre']);
        $mail->addAddress($correo, $nombres);

        $mail->isHTML(true);
        $mail->Subject = 'Tu código de verificación — CTRL Z';
        $mail->Body    = "<p>Hola {$nombres},</p>
            <p>Tu código de verificación para ingresar a CTRL Z es:</p>
            <h2 style='letter-spacing:6px'>{$codigo}</h2>
            <p>Este código vence en 10 minutos. Si no fuiste tú, ignora este correo.</p>";
        $mail->AltBody = "Tu código de verificación es: {$codigo} (vence en 10 minutos)";

        $mail->send();
    } catch (PHPMailerException $e) {
        error_log('CTRL Z Mailer: ' . $mail->ErrorInfo);
        responder(false, 'No se pudo enviar el correo con el código. Intenta de nuevo.', null, 500);
    }
}

/**
 * PASO 2 del login: valida el código de 4 dígitos ingresado
 * por el usuario. Si es correcto, recién ahí se abre la sesión
 * real con los datos guardados como "pendientes" en el paso 1.
 */
function verificarCodigo()
{
    $correo = strtolower(trim(isset($_POST['correo']) ? $_POST['correo'] : ''));
    $codigo = trim(isset($_POST['codigo']) ? $_POST['codigo'] : '');

    if ($correo === '' || $codigo === '') {
        responder(false, 'Faltan datos.', null, 422);
    }

    // El correo debe coincidir con el que quedó pendiente de verificar
    // en este mismo navegador/sesión (evita que alguien intente
    // verificar un código ajeno adivinando el correo).
    if (!isset($_SESSION['pendiente_correo']) || $_SESSION['pendiente_correo'] !== $correo) {
        responder(false, 'Tu sesión de verificación expiró. Vuelve a iniciar sesión.', null, 401);
    }

    $persona = new Persona();
    $registro = $persona->buscarCodigoVigente($correo);

    if (!$registro) {
        responder(false, 'No hay un código pendiente para este correo. Solicita uno nuevo.', null, 404);
    }

    if ((int) $registro['intentos'] >= 5) {
        responder(false, 'Demasiados intentos. Solicita un nuevo código.', null, 429);
    }

    if (strtotime($registro['expira_en']) < time()) {
        responder(false, 'El código expiró. Solicita uno nuevo.', null, 401);
    }

    if (!hash_equals($registro['codigo'], $codigo)) {
        $persona->incrementarIntentosCodigo($registro['id']);
        responder(false, 'Código incorrecto.', null, 401);
    }

    $persona->marcarCodigoUsado($registro['id']);

    $paginas = array(
        1 => 'admin/index.php',
        2 => 'docente/index.php',
        3 => 'estudiante/index.php',
    );

    $tipoper = (int) $_SESSION['pendiente_tipoper'];

    // Código correcto: recién aquí se abre la sesión autenticada.
    session_regenerate_id(true);
    $_SESSION['persona_id']        = (int) $_SESSION['pendiente_id'];
    $_SESSION['persona_nombres']   = $_SESSION['pendiente_nombres'];
    $_SESSION['persona_apellidos'] = $_SESSION['pendiente_apellidos'];
    $_SESSION['persona_correo']    = $_SESSION['pendiente_correo'];
    $_SESSION['persona_tipoper']   = $tipoper;

    unset($_SESSION['pendiente_id'], $_SESSION['pendiente_nombres'], $_SESSION['pendiente_apellidos'],
          $_SESSION['pendiente_correo'], $_SESSION['pendiente_tipoper']);

    responder(true, 'Bienvenido/a, ' . $_SESSION['persona_nombres'] . '.', array(
        'redirect' => $paginas[$tipoper],
    ), 200);
}

/**
 * Reenvía un nuevo código si el usuario no lo recibió a tiempo.
 * Solo funciona si ya pasó por loginPersona() en esta sesión.
 */
function reenviarCodigo()
{
    if (!isset($_SESSION['pendiente_correo'])) {
        responder(false, 'Tu sesión de verificación expiró. Vuelve a iniciar sesión.', null, 401);
    }

    $persona = new Persona();
    enviarCodigoAlCorreo($persona, $_SESSION['pendiente_correo'], $_SESSION['pendiente_nombres']);

    responder(true, 'Te enviamos un nuevo código a tu correo.', null, 200);
}

/**
 * Genera un número de 0 a 9999 para el código de verificación.
 * random_int() recién existe desde PHP 7.0; en WampServer 2.5
 * (PHP 5.5.12) se usa mt_rand() como respaldo.
 */
function generarNumeroAleatorio4Digitos()
{
    if (function_exists('random_int')) {
        return random_int(0, 9999);
    }

    return mt_rand(0, 9999);
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
