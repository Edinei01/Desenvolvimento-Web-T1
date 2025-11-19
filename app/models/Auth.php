<?php 

    namespace app\models;

    require_once __DIR__ . '/../core/Database.php';
    require_once __DIR__ . '/User.php';

    use app\core\Database;
    use app\models\User;
    use PDO;
    use PDOException;

    class Auth {

        private static $connection = null;
        private ?string $email;
        private ?string $password;

        public function __construct(?string $email = null, ?string $password = null){
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

        // SELECT PASS FROM `tb_user` WHERE EMAIL = "edinei@email.com";

        public function login(): array {

            if ($this->isEmptyFields($this->email, $this->password)) {
                return [
                    "status" => "error",
                    "message" => "Email e senha são obrigatórios",
                    "invalid_field" => "email"
                ];
            }

            $sanitized = $this->sanitize();
            if (is_array($sanitized)) return $sanitized;

            try {

            $user = User::loadByEmail($this->email);

            if (!$user) {
                return [
                    "status" => "error",
                    "message" => "Email incorreto",
                    "invalid_field" => "email"
                ];
            }

            if (!password_verify($this->password, $user->getPassword())) {
                return [
                    "status" => "error",
                    "message" => "Senha incorreta",
                    "invalid_field" => "password"
                ];
            }

            session_start();
            $_SESSION['user'] = $this->email;

            return [
                "status" => "success",
                "message" => "Login realizado com sucesso",
                "invalid_field" => null,
                "user" => $this->email
            ];

            } catch (PDOException $e) {
                return [
                    "status" => "error",
                    "message" => "Erro no servidor",
                    "debug" => $e->getMessage()
                ];
            }
        }

        public function existsEmail(): bool{
            $user = new User();
            $user->setEmailAddress($this->email);
            return $user->existsEmail();
        }

        public function logout(): array {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Limpa sessão
            $_SESSION = [];

            // Remove cookie
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }

            session_destroy();

            return [
                'status' => 'success',
                'message' => 'Sessão destruída, usuário deslogado'
            ];
        }
    }
