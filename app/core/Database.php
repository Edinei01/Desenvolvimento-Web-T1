<?php

    namespace app\core;

    require_once __DIR__ . '/../../config/Config.php';

    use mysqli;
    use Config\Config;

    class Database {

        private $host = Config::DB_HOST;
        private $user = Config::DB_USER;
        private $pass = Config::DB_PASS;
        private $dbName = Config::DB_NAME;
        private $conn = null;
        private static $instance = null;

        // Construtor privado
        private function __construct() {
            $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbName);

            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            }
        }

        // Método estático para retornar a conexão
        public static function getConnection() {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance->conn;
        }

        // Método estático para fechar a conexão
        public static function setCloseConnection() {
            if (isset(self::$instance) && isset(self::$instance->conn)) {
                self::$instance->conn->close();
                self::$instance->conn = null;
                self::$instance = null;
            }
        }
    }

?>