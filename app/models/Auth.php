<?php 

namespace app\models;

require_once __DIR__ . '/../core/Database.php';

use app\core\Database;
use PDO;
use PDOException;

class Auth {

    private static $connection = null;
    private ?string $email;
    private ?string $password;

    public function __construct(?string $email = null, ?string $password = null)
    {
        $this->email = $email;
        $this->password = $password;

        if (!self::$connection) {
            self::$connection = Database::getConnection();
        }
    }

    private function isEmptyFields(...$fields){
        foreach ($fields as $field) {
            if (empty($field)) return true;
        }
        return false;
    }

    private function sanitize() {

        $this->email = htmlspecialchars(trim($this->email));
        $this->password = htmlspecialchars(trim($this->password));

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            return [
                "status" => "error",
                "message" => "E-mail inválido",
                "invalid_field" => "email"
            ];
        }

        return true;
    }


    public function login(): array {

        if ($this->isEmptyFields($this->email, $this->password)) {
            return [
                "status" => "error",
                "message" => "Email e senha são obrigatórios",
                "invalid_field" => "email"
            ];
        }

        $sanitized = $this->sanitize();
        if (is_array($sanitized)) {
            return $sanitized;
        }

        try {

            $sql = "SELECT check_login_func(:email, :password) AS login_status";
            $stmt = self::$connection->prepare($sql);
            $stmt->bindValue(":email", $this->email, PDO::PARAM_STR);
            $stmt->bindValue(":password", $this->password, PDO::PARAM_STR);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $status = $row['login_status'] ?? null;

            if ($status === "ok") {

                session_start();
                $_SESSION["user"] = $this->email;

                return [
                    "status" => "success",
                    "message" => "Login realizado com sucesso",
                    "invalid_field" => null
                ];

            } else {
                return [
                    "status" => "error",
                    "message" => "Email ou senha incorretos",
                    "invalid_field" => $status 
                ];
            }

        } catch (PDOException $e) {
            return [
                "status" => "error",
                "message" => "Erro no servidor",
                "debug" => $e->getMessage()
            ];
        }
    }
}
