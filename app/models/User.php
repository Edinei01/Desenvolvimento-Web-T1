<?php

    namespace app\models;

    require_once __DIR__ . '/../core/Database.php';
    require_once __DIR__ . '/Auth.php';


    use app\core\Database;
    use PDO;
    use PDOException;
    use app\models\Auth;

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

        public static function loadByEmail(string $email): ?User{
            $userObj = new self();
            return $userObj->searchUserByEmail($email);
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

        public function existsEmail(): bool {
            
            $sql = "SELECT ID FROM TB_USER WHERE EMAIL = :email";
            $stmt = self::$connection->prepare($sql);
            $stmt->bindParam(":email", $this->email, PDO::PARAM_STR);
            $stmt->execute();
            
            $exists = ($stmt->fetchColumn() !== false);
            
            return !empty($exists);
        }
        
        private function insertUser() {       
            $name = $this->name;
            $email = $this->email;
            $password = password_hash($this->password, PASSWORD_DEFAULT);
            
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

        public function searchUserByEmail(string $email): ?User{
            $sql = "SELECT * FROM TB_USER WHERE EMAIL = :email";
            $stmt = self::$connection->prepare($sql);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $user = new User($row['NAME'], $row['EMAIL'], $row['PASS']);
                $user->id = $row['ID'];
                return $user;
            }

            return null;
        }
        
        public function jsonSerialize(): mixed {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password
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