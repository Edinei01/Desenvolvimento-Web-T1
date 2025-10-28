<?php 

    namespace app\models;

    require_once __DIR__ . '/../models/User.php';
    require_once __DIR__ . '/../enums/Category.php';
    require_once __DIR__ . '/../core/Database.php';

    use app\models\User;
    use app\enums\Category;
    use app\core\Database;

    class Contact {
    
        private ?User $user;
        private string $name;
        private string $email;
        private Category $category; 
        private string $phone;
        private string $notes;
        private static $connection = null;

        public function __construct(
            ?User $user = null,
            ?string $name = null,
            ?string $email = null,
            ?Category $category = null,
            ?string $phone = null,
            ?string $note = null
        ) {
            $this->user = $user;
            $this->name = $name ?? '';
            $this->email = $email ?? '';
            $this->category = $category ?? Category::default();
            $this->phone = $phone ?? '';
            $this->notes = $note ?? '';

            if (!self::$connection) {
                self::$connection = Database::getConnection();
            }

            self::$connection = self::$connection;
        }


        public function setName(string $name){
            $this->name = $name;
        }

        public function setEmail(string $email){
            $this->email = $email;
        }

        public function setCategory(?string $category){
            $this->category = Category::fromValue($category);
        }

        public function setPhone(string $phone){
            $this->phone = $phone;
        }

        public function setNote($note){
            $this->notes = $note;
        }


        public static function validateContactId() {
            $contactId = intval($_GET['id'] ?? 0);

            if ($contactId <= 0) {
                die(json_encode([
                    "status" => "error",
                    "message" => "ID de contato inválido."
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            }

                return $contactId;
        }

        private function getContacts(){
            // session_start();
            header('Content-Type: application/json');

            // Verifica se o usuário está logado
            // if (!isset($_SESSION['user'])) {
            //     echo json_encode(["status" => "error", "message" => "Usuário não autenticado"]);
            //     exit;
            // }

            // Busca ID do usuário logado
            // $email = $_SESSION['user'];

            $email = User::LoggedIn();
            $user = User::loadByEmail($email);

            // $user_id = null;
            $user_id = $user->getId();
            //  

            if (!$user_id) {
                echo json_encode(["status" => "error", "message" => "Usuário não encontrado"]);
                exit;
            }

            // Chama a procedure para listar contatos
            $stmt = self::$connection->prepare("CALL get_user_contacts(?)");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $contacts = [];
            while ($row = $result->fetch_assoc()) {
                $contacts[] = $row;
            }

            echo json_encode([
                "status" => "success",
                "data" => $contacts
            ]);

            $stmt->close();
            self::$connection::closeConnection();
        }


        // private function getContacts(int $id_contact): ?array {
        //     // session_start();
        //     header('Content-Type: application/json');

        //     // Verifica se o usuário está logado
        //     // if (!isset($_SESSION['user'])) {
        //     //     echo json_encode(["status" => "error", "message" => "Usuário não autenticado"]);
        //     //     exit;
        //     // }

        //     // Busca ID do usuário logado
        //     // $email = $_SESSION['user'];

        //     // $email = User::LoggedIn();
        //     // $this->user = User::loadByEmail(User::LoggedIn());

        //     // // $user_id = null;
        //     // // $user_id = $this->user->getId();
        //     // //  

        //     // if (!$this->user) {
        //     //     echo json_encode(["status" => "error", "message" => "Usuário não encontrado"]);
        //     //     exit;
        //     // }

        //     $sql = "select * from TB_CONTACT where ID = ?";
        //     $stmt = self::$connection->prepare($sql);
        //     $stmt->bind_param("i", $id_contact);
        //     $stmt->execute();
        //     $result = $stmt->get_result();
        //     $contact = $result->fetch_assoc();
        //     $stmt->close();
        //     self::$connection::closeConnection();
        //     return [
        //         "status" => "success",
        //         "data" => $contact
        //     ];
        //     // return $contact;
        //     // // Chama a procedure para listar contatos
        //     // $stmt = self::$connection->prepare("CALL get_user_contacts(?)");
        //     // $stmt->bind_param("i", $user_id);
        //     // $stmt->execute();
        //     // $result = $stmt->get_result();

        //     // $contacts = [];
        //     // while ($row = $result->fetch_assoc()) {
        //     //     $contacts[] = $row;
        //     // }

        //     // echo json_encode([
        //     //     "status" => "success",
        //     //     "data" => $contacts
        //     // ]);

        //     // $stmt->close();
        //     // self::$connection::closeConnection();
        // }

        // private function getContacts2(int $id_contact): ?array {
        //     // session_start();
        //     header('Content-Type: application/json');

        //     // Verifica se o usuário está logado
        //     // if (!isset($_SESSION['user'])) {
        //     //     echo json_encode(["status" => "error", "message" => "Usuário não autenticado"]);
        //     //     exit;
        //     // }

        //     // Busca ID do usuário logado
        //     // $email = $_SESSION['user'];

        //     // $email = User::LoggedIn();
        //     // $this->user = User::loadByEmail(User::LoggedIn());

        //     // // $user_id = null;
        //     // // $user_id = $this->user->getId();
        //     // //  

        //     // if (!$this->user) {
        //     //     echo json_encode(["status" => "error", "message" => "Usuário não encontrado"]);
        //     //     exit;
        //     // }

        //     $sql = "select * from TB_CONTACT where ID = ?";
        //     $stmt = self::$connection->prepare($sql);
        //     $stmt->bind_param("i", $id_contact);
        //     $stmt->execute();
        //     $result = $stmt->get_result();
        //     $contact = $result->fetch_assoc();
        //     $stmt->close();
        //     self::$connection::closeConnection();
        //     return [
        //         "status" => "success",
        //         "data" => $contact
        //     ];
        //     // return $contact;
        //     // // Chama a procedure para listar contatos
        //     // $stmt = self::$connection->prepare("CALL get_user_contacts(?)");
        //     // $stmt->bind_param("i", $user_id);
        //     // $stmt->execute();
        //     // $result = $stmt->get_result();

        //     // $contacts = [];
        //     // while ($row = $result->fetch_assoc()) {
        //     //     $contacts[] = $row;
        //     // }

        //     // echo json_encode([
        //     //     "status" => "success",
        //     //     "data" => $contacts
        //     // ]);

        //     // $stmt->close();
        //     // self::$connection::closeConnection();
        // }

        public function getContact(int $id_contact): ?array {
            // return $this->getContacts($id_contact);
            return $this->getContactById($id_contact);
        }

        private function add(){

            $userId = $this->user->getId();
            $name = $this->name;
            $email = $this->email;
            $phone = $this->phone;
            $categoryValue = $this->category->value;
            $notes = $this->notes;
           
            $stmt = self::$connection->prepare("CALL insert_contact(?, ?, ?, ?, ?, ?, @p_id, @p_success)");
            if (!$stmt) {
                http_response_code(500);
                echo json_encode([
                    "status" => "error",
                    "message" => "Erro ao preparar statement: " . self::$connection->error
                ]);
                exit;
            }

            // Vincula os parâmetros à procedure
            $stmt->bind_param("isssss", $userId, $name, $email, $phone, $categoryValue, $notes);

            // Executa a procedure
            if (!$stmt->execute()) {
                http_response_code(500);
                
                echo json_encode([
                    "status" => "error",
                    "message" => "Erro ao executar procedure: " . $stmt->error
                ]);
                $stmt->close();
                self::$connection->close();
                exit;
            }

            $stmt->close();

            $result = self::$connection->query("SELECT @p_id AS contact_id, @p_success AS success");

            if ($result && $row = $result->fetch_assoc()) {
                if ($row['success']) {
                    // Retorna sucesso com o ID do contato
                    echo json_encode([
                        "status" => "success",
                        "message" => "Contato adicionado com sucesso.",
                        "contact_id" => (int)$row['contact_id']
                    ]);
                } else {
                    // Caso a procedure falhe
                    http_response_code(500);
                    echo json_encode([
                        "status" => "error",
                        "message" => "Falha ao inserir contato."
                    ]);
                }
                $result->free();
            } else {
                // Erro ao recuperar saída da procedure
                http_response_code(500);
                echo json_encode([
                    "status" => "error",
                    "message" => "Erro ao recuperar saída da procedure."
                ]);
            }

            // Fecha conexão com o banco
            self::closeConnection();
          
        }
        
        public function addContact(User $user, string $name, string $email, string $category, string $phone, string $note){
            // Só configura os dados
            $this->user = $user;
            $this->name = $name;
            $this->email = $email;
            $this->phone = $phone;
            $this->notes = $note;
            $this->category = Category::fromValue($category); 

            // Chama o método privado que faz toda a lógica de DB
            $this->add();
        }

        private function getContactById(int $contactId): ?array {
            header('Content-Type: application/json');
    // if (!self::$connection) {
    //     self::$connection = Database::getConnection();
    // }

    $sql = "SELECT * FROM TB_CONTACTS WHERE ID = ?";
    $stmt = self::$connection->prepare($sql);
    $stmt->bind_param("i", $contactId);
    $stmt->execute();
    $result = $stmt->get_result();

    $contact = $result->fetch_assoc() ?: null;

    $stmt->close();
    return $contact;
}



        private function list(): array{

            // session_start();
            header('Content-Type: application/json');

            // // Verifica se o usuário está logado
            // if (!isset($_SESSION['user'])) {
            //     echo json_encode(["status" => "error", "message" => "Usuário não autenticado"]);
            //     exit;
            // }

            // Busca ID do usuário logado
            $email = $this->user->getEmailAddress();
            $sql = "SELECT ID FROM TB_USER WHERE EMAIL = ?";
            $stmt = self::$connection->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            
            $user_id = null;
            $stmt->bind_result($user_id);
            $stmt->fetch();
            $stmt->close();

            if (!$user_id) {
                echo json_encode(["status" => "error", "message" => "Usuário não encontrado"]);
                exit;
            }

            // Chama a procedure para listar contatos
            $stmt = self::$connection->prepare("CALL get_user_contacts(?)");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $contacts = [];
            while ($row = $result->fetch_assoc()) {
                $contacts[] = $row;
            }

            return [
                "status" => "success",
                "data" => $contacts
            ];

            $stmt->close();
            self::closeConnection();
        }

        public function listContact(): array {
            return $this->list();
        }





        private function delete(){}
        private function update(){}
        private function search(){}




        public function deleteContact(){}
        public function updateContact(){}
        public function searchContact(){}

        public static function closeConnection() {
            if (self::$connection) {
                self::$connection->close();
                self::$connection = null;
            }
        }
    }

//    $user = User::loadByEmail('edinei@email.com');
// $contact = new Contact();

// $contact->addContact($user, 'edinei almeida', 'edinei@email.com23','família','19988354700','qwert');
// $contact->addContact(1);


// $user = User::loadByEmail($_SESSION['user'] ?? '');

// $contact = new Contact();

// echo json_encode($contact->getContact(1), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>