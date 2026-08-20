<?php

require_once __DIR__ . '/../config/database.php';

// ---------------------------------------------------------------
// Polyfill de password_hash()/password_verify() para PHP < 5.5
// (WampServer 2.5 suele traer PHP 5.4, donde estas funciones
// todavía no existen de forma nativa).
// ---------------------------------------------------------------
if (!function_exists('hash_equals')) {
    function hash_equals($known, $given)
    {
        if (!is_string($known) || !is_string($given)) {
            return false;
        }
        $knownLen = strlen($known);
        if ($knownLen !== strlen($given)) {
            return false;
        }
        $result = 0;
        for ($i = 0; $i < $knownLen; $i++) {
            $result |= (ord($known[$i]) ^ ord($given[$i]));
        }
        return $result === 0;
    }
}

if (!function_exists('password_hash')) {
    define('PASSWORD_DEFAULT', 1);
    define('PASSWORD_BCRYPT', 1);

    function password_hash($password, $algo, $options = array())
    {
        $cost = isset($options['cost']) ? (int) $options['cost'] : 10;

        $rawSalt = function_exists('openssl_random_pseudo_bytes')
            ? openssl_random_pseudo_bytes(16)
            : uniqid('', true) . uniqid('', true);

        $salt = substr(str_replace('+', '.', base64_encode($rawSalt)), 0, 22);
        $prefix = sprintf('$2y$%02d$', $cost);

        return crypt($password, $prefix . $salt);
    }

    function password_verify($password, $hash)
    {
        if (!is_string($password) || !is_string($hash) || $hash === '') {
            return false;
        }
        $rehash = crypt($password, $hash);
        return hash_equals($hash, $rehash);
    }
}

class Persona
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    /**
     * Verifica si ya existe una persona con la misma cédula
     * o un usuario con el mismo correo.
     * (ci vive en `persona`, correo vive ahora en `usuario`).
     */
    public function existe($ci, $correo)
    {
        $sql = "SELECT p.id
                FROM persona p
                LEFT JOIN usuario u ON u.id_persona = p.id
                WHERE p.ci = :ci OR u.correo = :correo
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':ci' => $ci,
            ':correo' => $correo,
        ));

        return (bool) $stmt->fetch();
    }

    /**
     * Busca una persona por correo (usado para el login).
     * Hace JOIN con `usuario`, ya que correo/contrase/estado
     * de sesión viven ahí.
     * Devuelve el arreglo con sus datos, o null si no existe.
     */
    public function buscarPorCorreo($correo)
    {
        $sql = "SELECT p.id, p.nombres, p.apellidos, p.tipoper,
                       u.id AS id_usuario, u.correo, u.contrase, u.estado
                FROM usuario u
                INNER JOIN persona p ON p.id = u.id_persona
                WHERE u.correo = :correo
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':correo' => $correo));

        $fila = $stmt->fetch();

        return $fila ? $fila : null;
    }

    /**
     * Registra la persona y, en la misma operación, crea su
     * usuario (correo + contraseña) asociado por id_persona.
     * La contraseña se almacena con hash, nunca en texto plano.
     */
    public function registrar($datos)
    {
        try {
            $this->db->beginTransaction();

            $sqlPersona = "INSERT INTO persona
                        (nombres, apellidos, telefono, ci,
                         extension, f_nac, sexo, estcivil, tipoper, freg, estado)
                    VALUES
                        (:nombres, :apellidos, :telefono, :ci,
                         :extension, :f_nac, :sexo, :estcivil, :tipoper, NOW(), 1)";

            $stmtPersona = $this->db->prepare($sqlPersona);
            $stmtPersona->execute(array(
                ':nombres'   => $datos['nombres'],
                ':apellidos' => $datos['apellidos'],
                ':telefono'  => $datos['telefono'],
                ':ci'        => $datos['ci'],
                ':extension' => $datos['extension'],
                ':f_nac'     => $datos['f_nac'],
                ':sexo'      => $datos['sexo'],
                ':estcivil'  => $datos['estcivil'],
                ':tipoper'   => $datos['tipoper'],
            ));

            $idPersona = (int) $this->db->lastInsertId();

            $sqlUsuario = "INSERT INTO usuario
                        (id_persona, correo, contrase, estado)
                    VALUES
                        (:id_persona, :correo, :contrase, 1)";

            $stmtUsuario = $this->db->prepare($sqlUsuario);
            $stmtUsuario->execute(array(
                ':id_persona' => $idPersona,
                ':correo'     => $datos['correo'],
                ':contrase'   => password_hash($datos['password'], PASSWORD_DEFAULT),
            ));

            $this->db->commit();

            return $idPersona;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Devuelve todas las personas registradas junto con su correo
     * (tabla usuario), para listarlas en el panel de administrador.
     */
    public function listarTodos()
    {
        $sql = "SELECT p.id, p.nombres, p.apellidos, p.tipoper, p.estado,
                       u.correo
                FROM persona p
                LEFT JOIN usuario u ON u.id_persona = p.id
                ORDER BY p.nombres ASC, p.apellidos ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Actualiza el tipo de persona (1 Administrador, 2 Docente, 3 Estudiante).
     * Devuelve true si se modificó al menos una fila.
     */
    public function actualizarTipoPer($idPersona, $tipoper)
    {
        $tipoper = (int) $tipoper;

        if (!in_array($tipoper, array(1, 2, 3), true)) {
            throw new InvalidArgumentException('tipoper inválido');
        }

        $sql = "UPDATE persona SET tipoper = :tipoper WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':tipoper' => $tipoper,
            ':id'      => (int) $idPersona,
        ));

        return $stmt->rowCount() > 0;
    }

    /**
     * Busca una persona por su id (persona.id), trayendo también
     * su correo (tabla usuario), para precargar el formulario de edición.
     */
    public function buscarPorId($id)
    {
        $sql = "SELECT p.id, p.nombres, p.apellidos, p.telefono, p.tipoper,
                       p.ci, p.extension, p.f_nac, p.sexo, p.estcivil,
                       u.correo
                FROM persona p
                LEFT JOIN usuario u ON u.id_persona = p.id
                WHERE p.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':id' => (int) $id));

        $fila = $stmt->fetch();

        return $fila ? $fila : null;
    }

    /**
     * Verifica si un correo ya está en uso por otra persona distinta
     * a $idPersonaExcluir (para no chocar consigo misma al editar).
     */
    public function correoExiste($correo, $idPersonaExcluir = null)
    {
        $sql = "SELECT u.id FROM usuario u WHERE u.correo = :correo";
        $params = array(':correo' => $correo);

        if ($idPersonaExcluir !== null) {
            $sql .= " AND u.id_persona != :id_persona";
            $params[':id_persona'] = (int) $idPersonaExcluir;
        }

        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (bool) $stmt->fetch();
    }

    /**
     * Verifica si una cédula ya está en uso por otra persona distinta
     * a $idPersonaExcluir (para no chocar consigo misma al editar).
     */
    public function ciExiste($ci, $idPersonaExcluir = null)
    {
        $sql = "SELECT id FROM persona WHERE ci = :ci";
        $params = array(':ci' => $ci);

        if ($idPersonaExcluir !== null) {
            $sql .= " AND id != :id";
            $params[':id'] = (int) $idPersonaExcluir;
        }

        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (bool) $stmt->fetch();
    }

    /**
     * Actualiza todos los datos editables de una persona (los mismos
     * campos que el formulario de registro, salvo tipoper) y, en la
     * tabla usuario, el correo y (opcionalmente) la contraseña.
     * Si $datos['password'] viene vacío, la contraseña actual no se toca.
     */
    public function actualizarDatos($idPersona, $datos)
    {
        try {
            $this->db->beginTransaction();

            $sqlPersona = "UPDATE persona
                        SET nombres = :nombres, apellidos = :apellidos, telefono = :telefono,
                            ci = :ci, extension = :extension, f_nac = :f_nac,
                            sexo = :sexo, estcivil = :estcivil
                        WHERE id = :id";

            $stmtPersona = $this->db->prepare($sqlPersona);
            $stmtPersona->execute(array(
                ':nombres'   => $datos['nombres'],
                ':apellidos' => $datos['apellidos'],
                ':telefono'  => $datos['telefono'],
                ':ci'        => $datos['ci'],
                ':extension' => $datos['extension'],
                ':f_nac'     => $datos['f_nac'],
                ':sexo'      => $datos['sexo'],
                ':estcivil'  => $datos['estcivil'],
                ':id'        => (int) $idPersona,
            ));

            if (!empty($datos['password'])) {
                $sqlUsuario = "UPDATE usuario
                            SET correo = :correo, contrase = :contrase
                            WHERE id_persona = :id_persona";
                $stmtUsuario = $this->db->prepare($sqlUsuario);
                $stmtUsuario->execute(array(
                    ':correo'     => $datos['correo'],
                    ':contrase'   => password_hash($datos['password'], PASSWORD_DEFAULT),
                    ':id_persona' => (int) $idPersona,
                ));
            } else {
                $sqlUsuario = "UPDATE usuario
                            SET correo = :correo
                            WHERE id_persona = :id_persona";
                $stmtUsuario = $this->db->prepare($sqlUsuario);
                $stmtUsuario->execute(array(
                    ':correo'     => $datos['correo'],
                    ':id_persona' => (int) $idPersona,
                ));
            }

            $this->db->commit();

            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Da de baja (0) o de alta (1) a una persona: actualiza tanto
     * persona.estado como usuario.estado (este último es el que
     * bloquea el login en AuthController::loginPersona()).
     * Si se reactiva (estado 1), también se limpia su historial de
     * intentos fallidos, para que empiece con el contador en cero.
     */
    public function actualizarEstado($idPersona, $estado)
    {
        $estado = (int) $estado;

        if (!in_array($estado, array(0, 1), true)) {
            throw new InvalidArgumentException('estado inválido');
        }

        try {
            $this->db->beginTransaction();

            $stmtPersona = $this->db->prepare("UPDATE persona SET estado = :estado WHERE id = :id");
            $stmtPersona->execute(array(
                ':estado' => $estado,
                ':id'     => (int) $idPersona,
            ));

            $stmtUsuario = $this->db->prepare("UPDATE usuario SET estado = :estado WHERE id_persona = :id_persona");
            $stmtUsuario->execute(array(
                ':estado'     => $estado,
                ':id_persona' => (int) $idPersona,
            ));

            if ($estado === 1) {
                $stmtIdUsuario = $this->db->prepare("SELECT id FROM usuario WHERE id_persona = :id_persona LIMIT 1");
                $stmtIdUsuario->execute(array(':id_persona' => (int) $idPersona));
                $filaUsuario = $stmtIdUsuario->fetch();

                if ($filaUsuario) {
                    $stmtLimpiar = $this->db->prepare("DELETE FROM loginerror WHERE id_usuario = :id_usuario");
                    $stmtLimpiar->execute(array(':id_usuario' => (int) $filaUsuario['id']));
                }
            }

            $this->db->commit();

            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Registra un intento de login fallido (contraseña incorrecta)
     * para un usuario existente.
     */
    public function registrarErrorLogin($idUsuario)
    {
        $sql = "INSERT INTO loginerror (id_usuario, fecha, ip)
                VALUES (:id_usuario, NOW(), :ip)";

        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':id_usuario' => (int) $idUsuario,
            ':ip'         => $ip,
        ));
    }

    /**
     * Cuenta cuántos intentos fallidos acumulados tiene un usuario
     * desde el último login exitoso o la última reactivación.
     */
    public function contarErroresLogin($idUsuario)
    {
        $sql = "SELECT COUNT(*) AS total FROM loginerror WHERE id_usuario = :id_usuario";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':id_usuario' => (int) $idUsuario));

        $fila = $stmt->fetch();

        return $fila ? (int) $fila['total'] : 0;
    }

    /**
     * Limpia el historial de intentos fallidos de un usuario
     * (se usa tras un login exitoso).
     */
    public function limpiarErroresLogin($idUsuario)
    {
        $sql = "DELETE FROM loginerror WHERE id_usuario = :id_usuario";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':id_usuario' => (int) $idUsuario));
    }

    // =========================================================
    // Verificación de login por código de 4 dígitos (2do factor)
    // =========================================================

    /**
     * Invalida (marca como usados) los códigos anteriores no usados
     * de un correo, antes de generar uno nuevo.
     */
    public function invalidarCodigosAnteriores($correo)
    {
        $sql = "UPDATE codigos_verificacion SET usado = 1 WHERE correo = :correo AND usado = 0";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':correo' => $correo));
    }

    /**
     * Guarda un nuevo código de verificación para un correo,
     * con su fecha de expiración.
     */
    public function guardarCodigoVerificacion($correo, $codigo, $expiraEn)
    {
        $sql = "INSERT INTO codigos_verificacion (correo, codigo, expira_en)
                VALUES (:correo, :codigo, :expira_en)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            ':correo'    => $correo,
            ':codigo'    => $codigo,
            ':expira_en' => $expiraEn,
        ));
    }

    /**
     * Busca el código de verificación vigente (no usado) más reciente
     * para un correo. Devuelve null si no hay ninguno.
     */
    public function buscarCodigoVigente($correo)
    {
        $sql = "SELECT id, codigo, expira_en, intentos
                FROM codigos_verificacion
                WHERE correo = :correo AND usado = 0
                ORDER BY id DESC LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':correo' => $correo));

        $fila = $stmt->fetch();

        return $fila ? $fila : null;
    }

    /**
     * Suma un intento fallido a un código de verificación.
     */
    public function incrementarIntentosCodigo($idCodigo)
    {
        $sql = "UPDATE codigos_verificacion SET intentos = intentos + 1 WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':id' => (int) $idCodigo));
    }

    /**
     * Marca un código de verificación como usado (ya no puede
     * reutilizarse para un futuro intento de login).
     */
    public function marcarCodigoUsado($idCodigo)
    {
        $sql = "UPDATE codigos_verificacion SET usado = 1 WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(':id' => (int) $idCodigo));
    }
}
