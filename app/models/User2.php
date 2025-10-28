<?php

namespace app\models;

require_once __DIR__ . '/../core/Database.php';


use app\core\Database;


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

       self::$connection = self::$connection;
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

    public static function loadByEmail(string $email){
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

    private function existsEmail() {
        $sql = "SELECT ID FROM TB_USER WHERE EMAIL = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $stmt->store_result();

        $exists = $stmt->num_rows > 0;
        $stmt->close();

        return $exists;
    }

    private function insertUser() {
        $sql = "CALL insert_user(?, ?, ?, @p_id, @success)";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("sss", $this->name, $this->email, $this->password);
        $stmt->execute();
        $stmt->close();

        $sql = "SELECT @p_id AS user_id, @success AS success";
        $result = self::$connection->query($sql);
        $row = $result->fetch_assoc();
        $this->id = $row['user_id'];
        return $row;
    }

    // ---------- MÉTODO PÚBLICO ----------
    public function createUser() {
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

    private function loginUser(){

        if(!$this->isSanitized()){
            echo json_encode(['status' => 'error', 'message' => 'Campos inválidos']);
            exit;
        }

        // Valida campos obrigatórios
        if ($this->isEmptyFields($this->email, $this->password)) {
            echo json_encode(['status' => 'error', 'message' => 'Email and password are required']);
            exit;
        }

        // Chama a função no MySQL
        $sql = "SELECT check_login_func(?, ?) AS login_status";
        $stmt = self::$connection->stmt_init();
        $stmt->prepare($sql);
        $stmt->bind_param('ss', $this->email, $this->password);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        self::$connection->close();

        $status = $row['login_status'];

        // Retorna JSON de acordo com o resultado
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
                'invalid_field' => $status // 'email' ou 'password'
            ]);
        }
    }

    public function login(){
        return $this->loginUser();
    }

    
    public function deleteUser() {}
    
    private function searchUserByID(int $id): ?User {
        $sql = "SELECT * FROM TB_USER WHERE ID = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row) {
            $user = new User($row['NAME'], $row['EMAIL']);
            $user->id = $row['ID'];
            // $user->setName($row['NAME']);
            // $user->setEmailAddress($row['EMAIL']);
            return $user;
        }

        return null;
    }


    public function getUserIdByEmail(string $email): ?int {
        $sql = "SELECT ID FROM tb_user WHERE EMAIL = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        $userId = null;
        if ($result && $row = $result->fetch_assoc()) {
            $userId = (int) $row['ID'];
        }

        $stmt->close(); 
        return $userId;
    }

    private function searchUserByEmail(string $email):User|null{
        $sql = "SELECT * FROM TB_USER WHERE EMAIL = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row) {
            $user = new User($row['NAME'], $row['EMAIL']);
            $user->id = $row['ID'];
            // $user->setName($row['NAME']);
            // $user->setEmailAddress($row['EMAIL']);
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

// $users = [];
// for ($i = 1; $i < 15; $i++) {
//     $users[] = User::loadByID($i); // carrega pelo ID
// }

// // imprime os usuários carregados pelo ID
// foreach ($users as $user) {
//     echo json_encode($user, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
//     echo '<br>';
// }

// // carrega os mesmos usuários pelo e-mail usando os dados de $users
// $usersByEmail = [];
// foreach ($users as $user) {
//     $usersByEmail[] = User::loadByEmail($user->getEmailAddress());
// }

// echo '<br>Carregados pelo e-mail:<br>';

// // imprime os usuários carregados pelo e-mail
// foreach ($usersByEmail as $user) {
//     echo json_encode($user, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
//     echo '<br>';
// }

















<?php

namespace app\models;

require_once __DIR__ . '/../core/Database.php';


use app\core\Database;


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
        $sql = "SELECT ID FROM TB_USER WHERE EMAIL = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $stmt->store_result();

        $exists = $stmt->num_rows > 0;
        $stmt->close();

        return $exists;
    }

    private function insertUser() {
        $sql = "CALL insert_user(?, ?, ?, @p_id, @success)";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("sss", $this->name, $this->email, $this->password);
        $stmt->execute();
        $stmt->close();

        $sql = "SELECT @p_id AS user_id, @success AS success";
        $result = self::$connection->query($sql);
        $row = $result->fetch_assoc();
        $this->id = $row['user_id'];
        return $row;
    }

    // ---------- MÉTODO PÚBLICO ----------
    public function createUser() {
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

    private function loginUser(){

        if(!$this->isSanitized()){
            echo json_encode(['status' => 'error', 'message' => 'Campos inválidos']);
            exit;
        }

        // Valida campos obrigatórios
        if ($this->isEmptyFields($this->email, $this->password)) {
            echo json_encode(['status' => 'error', 'message' => 'Email and password are required']);
            exit;
        }

        // Chama a função no MySQL
        $sql = "SELECT check_login_func(?, ?) AS login_status";
        $stmt = self::$connection->stmt_init();
        $stmt->prepare($sql);
        $stmt->bind_param('ss', $this->email, $this->password);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        self::$connection->close();

        $status = $row['login_status'];

        // Retorna JSON de acordo com o resultado
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
                'invalid_field' => $status // 'email' ou 'password'
            ]);
        }
    }

    public function login(){
        return $this->loginUser();
    }

    public function deleteUser() {}
    
    private function searchUserByID(int|string $id): ?User {
        $user_id = (int) $id;

        $sql = "SELECT * FROM TB_USER WHERE ID = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row) {
            $user = new User($row['NAME'], $row['EMAIL']);
            $user->id = $row['ID'];
            return $user;
        }

        return null;
    }


    // public function getUserIdByEmail(string $email): ?int {
    //     $sql = "SELECT ID FROM tb_user WHERE EMAIL = ?";
    //     $stmt = self::$connection->prepare($sql);
    //     $stmt->bind_param("s", $email);
    //     $stmt->execute();
    //     $result = $stmt->get_result();

    //     $userId = null;
    //     if ($result && $row = $result->fetch_assoc()) {
    //         $userId = (int) $row['ID'];
    //     }

    //     $stmt->close(); 
    //     return $userId;
    // }

    private function searchUserByEmail(string $email): ?User{
        $sql = "SELECT * FROM TB_USER WHERE EMAIL = ?";
        $stmt = self::$connection->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row) {
            $user = new User($row['NAME'], $row['EMAIL']);
            $user->id = $row['ID'];
            // $user->setName($row['NAME']);
            // $user->setEmailAddress($row['EMAIL']);
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

// $users = [];
// echo 'com id = int';
// echo'<br>';
// for ($i = 1; $i < 15; $i++) {
//     $users[] = User::loadByID($i); // carrega pelo ID
// }

// // imprime os usuários carregados pelo ID
// foreach ($users as $user) {
//     echo json_encode($user, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
//     echo '<br>';
// }

// $users2=[];
// echo 'com id = string';
// echo'<br>';
// for ($i = 1; $i < 15; $i++) {
//     $str = (string) $i;
//     $users2[] = User::loadByID($str); // carrega pelo ID
// }


// foreach ($users2 as $user) {
//     echo json_encode($user, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
//     echo '<br>';
// }



// // carrega os mesmos usuários pelo e-mail usando os dados de $users
// $usersByEmail = [];
// foreach ($users as $user) {
//     $usersByEmail[] = User::loadByEmail($user->getEmailAddress());
// }

// echo '<br>Carregados pelo e-mail:<br>';

// // imprime os usuários carregados pelo e-mail
// foreach ($usersByEmail as $user) {
//     echo json_encode($user, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
//     echo '<br>';
// }
