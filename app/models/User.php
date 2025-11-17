<?php

    namespace app\models;

    require_once __DIR__ . '/../core/Database.php';

    use app\core\Database;
    use PDO;
    use PDOException;

    class User implements \JsonSerializable{

        private int $id;
        private ?string $name;
        private ?string $email;
        private ?string $password;
        private static $connection = null;

        // ---------- CONSTRUTOR ----------
        public function __construct(?string $name = null,?string $email = null, ?string $password = null) {
            $this->name = $name;
            $this->email = $email;
            $this->password = $password;

            // Abre a conexão se ainda não existir
            if (!self::$connection) {
                self::$connection = Database::getConnection();
            }
        }

        // ---------- SETTERS ----------
        public function setName(string $name) {
            $this->name = $name;
        }

        public function setEmailAddress(string $email) {
            $this->email = $email;
        }

        public function setPassword(string $password) {
            $this->password = $password;
        }

        // ---------- GETTERS ----------
        public function getName() {
            return $this->name;
        }

        public function getId():int{
            return $this->id;
        }

        public function getEmailAddress() {
            return $this->email;
        }

        public function getPassword() {
            return $this->password;
        }

        public static function loadByID(int $id): User {
            $userObj = new self();
            return $userObj->searchUserByID($id);
        }

        public static function loadByEmail(string $email): User{
            $userObj = new self();
            return $userObj->searchUserByEmail($email);
        }

        public static function LoggedIn(bool $redirect = true): ?string {
            session_start();

            if (!isset($_SESSION['user'])) {
                if ($redirect) {
                    header("Location: ../../public/index.php");
                    exit;
                } else {
                    echo json_encode([
                        "status" => "error",
                        "message" => "Usuário não logado."
                    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                    exit;
                }
            }
            return $_SESSION['user'];
        }


        // ---------- MÉTODOS PRIVADOS ----------
        private function isEmptyFields(...$fields){
            foreach ($fields as $field) {
                if (empty($field)) return true;
            }
            return false;
        }

        private function isSanitized() {
            $this->name = htmlspecialchars(trim($this->name));
            $this->email = htmlspecialchars(trim($this->email));
            $this->password = htmlspecialchars(trim($this->password));

            if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                echo " E-mail inválido";
                return false;
            }

            return true;
        }

        private function existsEmail() {

            $sql = "SELECT ID FROM TB_USER WHERE EMAIL = :email";
            $stmt = self::$connection->prepare($sql);
            $stmt->bindParam(":email", $this->email, PDO::PARAM_STR);
            $stmt->execute();

            $exists = ($stmt->fetchColumn() !== false);

            return $exists;
        }

        private function insertUser() {       
            $name = $this->name;
            $email = $this->email;
            $password = $this->password;

            $sql = "CALL insert_user(:name, :email, :password, @p_id, @success)";
            $stmt = self::$connection->prepare($sql);
            $stmt->bindParam(":name", $name, PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->bindParam(":password", $password, PDO::PARAM_STR);
            $stmt->execute();

            $stmt->closeCursor();

            $sql = "SELECT @p_id AS user_id, @success AS success";
            $stmt = self::$connection->query($sql);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['user_id'];
            return $row;
        }

        // ---------- MÉTODO PÚBLICO ----------
        public function registerUser() {
            header('Content-Type: application/json; charset=utf-8');

            // Valida campos
            if ($this->isEmptyFields($this->name, $this->email, $this->password) || !$this->isSanitized()) {
                echo json_encode(['status' => 'error', 'message' => 'Campos inválidos'], JSON_UNESCAPED_UNICODE);
                return;
            }

            // Verifica se email já existe
            if ($this->existsEmail()) {
                echo json_encode(['status' => 'error', 'message' => 'Email já cadastrado'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                return;
            }

            // Insere usuário
            $row = $this->insertUser();
            $p_id = $row['user_id'];
            $success = $row['success'];

            // Retorno
            if ($success) {
                session_start();
                $_SESSION['user'] = $this->email;
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Usuário inserido com sucesso!',
                    'user_id' => $p_id
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Falha ao cadastrar usuário'
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
        }

        private function loginUser() {
            if (!$this->isSanitized()) {
                echo json_encode(['status' => 'error', 'message' => 'Campos inválidos']);
                exit;
            }

            if ($this->isEmptyFields($this->email, $this->password)) {
                echo json_encode(['status' => 'error', 'message' => 'Email e senha são obrigatórios']);
                exit;
            }

            try {
                $sql = "SELECT check_login_func(:email, :password) AS login_status";
                $stmt = self::$connection->prepare($sql);
                $stmt->bindParam(':email', $this->email);
                $stmt->bindParam(':password', $this->password);
                $stmt->execute();

                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $status = $row['login_status'] ?? null;
                if ($status === 'ok') {
                    session_start();
                    $_SESSION['user'] = $this->email;
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Login successful',
                        'invalid_field' => null
                    ]);
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Invalid username or password',
                        'invalid_field' => $status 
                    ]);
                }

            } catch (PDOException $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Erro ao realizar login: ' . $e->getMessage()
                ]);
            }
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

        public function login(){
            return $this->loginUser();
        }
        
        public function deleteUser() {}
        
        private function searchUserByID(int|string $id): ?User {
            $user_id = (int) $id;

            $sql = "SELECT * FROM TB_USER WHERE ID = :id";
            $stmt = self::$connection->prepare($sql);
            $stmt->bindParam(":id", $user_id, PDO::PARAM_INT);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $user = new User($row['NAME'], $row['EMAIL']);
                $user->id = $row['ID'];
                return $user;
            }

            return null;
        }

        private function searchUserByEmail(string $email): ?User{
            $sql = "SELECT * FROM TB_USER WHERE EMAIL = :email";
            $stmt = self::$connection->prepare($sql);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $user = new User($row['NAME'], $row['EMAIL']);
                $user->id = $row['ID'];
                return $user;
            }

            return null;
        }
        
        public function jsonSerialize(): mixed {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email
            ];
        }

        // ---------- MÉTODO ESTÁTICO PARA FECHAR A CONEXÃO ----------
        public static function closeConnection() {
            if (self::$connection) {
                self::$connection->close();
                self::$connection = null;
            }
        }
    }