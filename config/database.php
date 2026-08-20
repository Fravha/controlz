<?php
/**
 * Configuración de conexión MySQL para CTRL Z.
 * WAMP / localhost
 */

class Database
{
    private $host = 'localhost';
    private $dbName = 'updsctrolz';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';

    public function connect()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset={$this->charset}";

        $options = array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        );

        return new PDO($dsn, $this->username, $this->password, $options);
    }
}