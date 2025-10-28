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
            $this->category = Category::fromValue($category); // já seta o enum

            // Chama o método privado que faz toda a lógica de DB
            $this->add();
        }


        private function delete(){}
        private function update(){}
        private function search(){}
        private function list(){}




        public function deleteContact(){}
        public function updateContact(){}
        public function searchContact(){}
        public function listContact(){}

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
?>