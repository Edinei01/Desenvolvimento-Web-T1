<?php

    namespace app\core;

    require_once __DIR__ . '/../../config/Config.php';

    use Config\Config;
    use Exception;
    use PDO;
    use PDOException;

    class Database {

        private $host = Config::DB_HOST;
        private $user = Config::DB_USER;
        private $pass = Config::DB_PASS;
        private $dbName = Config::DB_NAME;
        private ?PDO $conn = null;
        private static ?Database $instance = null;

        // Construtor privado
        private function __construct() {
            try {
                $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbName", $this->user, $this->pass);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Connection failed: " .$e->getMessage());
            } catch(Exception $e){
                die("Generic error: " .$e->getMessage());
            }
        }

        public static function getConnection(): PDO {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance->conn;
        }
    }

?>