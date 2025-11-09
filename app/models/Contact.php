<?php 

    namespace app\models;

    require_once __DIR__ . '/../models/User.php';
    require_once __DIR__ . '/../enums/Category.php';
    require_once __DIR__ . '/../core/Database.php';

    use app\models\User;
    use app\enums\Category;
    use app\core\Database;
    use Exception;

    class Contact implements \JsonSerializable{
    
        private ?User $user;
        private ?int $id;
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

        private function setId(?int $id){
            $this->id = $id;
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

        public function setUser(User $user){
            $this->user = $user;
        }

        public function setNote($note){
            $this->notes = $note;
        }

        public function getId(): ?int{
            return $this->id;
        }

        public function getName(): string{
            return $this->name;
        }

        public function getEmail(): string{
            return $this->email;
        }

        public function getCategory(): Category{
            return $this->category;
        }

        public function getPhone(): string{
            return $this->phone;
        }

        public function getNotes(): string{
            return $this->notes;
        }

        public function getUser(): ?User{
            return $this->user;
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

        public static function loadByID(int $id): ?Contact{
           
            $data = new self();
            
            $data = $data->getContactById($id);

            $contact = new self();
            $contact->setId($data['ID'] ?? null);
            $contact->setName($data['NAME'] ?? '');
            $contact->setEmail($data['EMAIL'] ?? '');
            $contact->setCategory($data['CATEGORY'] ?? 'Outros');
            $contact->setPhone($data['PHONE'] ?? '');
            $contact->setNote($data['NOTES'] ?? '');
            return  $contact;
        }

        public static function loadByEmail(string $email): ?array {
            header('Content-Type: application/json');

            $sql = "SELECT * FROM TB_CONTACTS WHERE EMAIL = ?";
            $stmt = self::$connection->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            $data = $result->fetch_assoc();

            if ($data) {
                
                $stmt->close();
                return $data;
            }

            $stmt->close();
            return null;
        }

        private function getContacts(){
            // session_start();
            header('Content-Type: application/json');

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

        public function getContact(int $id_contact): ?array {
            // return $this->getContacts($id_contact);
            return $this->getContactById($id_contact);
        }

        public function getContactID(): ?int{
            
            $email = $this->email;
            $sql = "SELECT ID FROM TB_CONTACTS WHERE EMAIL = ?";
            $stmt = self::$connection->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $id = null;
            $stmt->bind_result($id);
            $stmt->fetch();
            $stmt->close();
             
            return $id;
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
                    $this->id = (int)$row['contact_id'];
                    http_response_code(201);
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
                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
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
                echo json_encode(["status" => "error", "message" => "Usuário não encontrado"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
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

        private function update(){

            header('Content-Type: application/json');

            if (!isset($this->id)) {
                echo json_encode(['status' => 'error', 'message' => 'ID do contato não fornecido'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                exit;
            }

            $contactId = $this->id;
            $name      = $this->name;
            $email     = $this->email;
            $phone     = $this->phone;
            $category  = $this->category->value;
            $notes     = $this->notes;

            if ($contactId <= 0 || empty($name)) {
                echo json_encode(['status' => 'error', 'message' => 'Dados inválidos'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                exit;
            }

            // Atualizar usando procedure
            $sql = "CALL update_contact(?, ?, ?, ?, ?, ?, @success)";
            $stmt = self::$connection->prepare($sql);
            $stmt->bind_param("isssss", $contactId, $name, $email, $phone, $category, $notes);

            if (!$stmt->execute()) {
                echo json_encode(['status' => 'error', 'message' => 'Falha ao atualizar contato: ' . $stmt->error], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                $stmt->close();
                self::$connection->close();
                exit;
            }

            $stmt->close();

            // Recupera valor da variável de saída @success
            $result = self::$connection->query("SELECT @success AS success");
            $row = $result->fetch_assoc();
            $success = $row['success'] ?? 0;

            if ($success) {
                return ['status' => 'success', 'message' => 'Contato atualizado com sucesso'];
            } else {
                return ['status' => 'error', 'message' => 'Falha ao atualizar contato'];
            }

            self::$connection->close();
        }

        public function updateContact(){
            return json_encode($this->update(),JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        private function delete(){

            header('Content-Type: application/json; charset=UTF-8');

            $contactId = $this->id;

            try {
                //  Prepara a chamada da procedure com parâmetro de saída
                $sql = "CALL delete_contact(?, @resultado)";
                $stmt =  self::$connection->prepare($sql);
                if (!$stmt) {
                    throw new Exception('Erro ao preparar statement: ' . self::$connection->error);
                }

                $stmt->bind_param("i", $contactId);
                if (!$stmt->execute()) {
                    throw new Exception('Erro ao executar statement: ' . $stmt->error);
                }
                $stmt->close();

                //  Busca o valor do parâmetro de saída
                $result = self::$connection->query("SELECT @resultado AS resultado");
                if (!$result) {
                    throw new Exception('Erro ao buscar resultado: ' . self::$connection->error);
                }

                $row = $result->fetch_assoc();
                $mensagem = $row['resultado'] ?? 'Erro desconhecido';

                // Retorna JSON apropriado
                if ($mensagem === 'deletado com sucesso') {
                    return ['status' => 'success', 'message' => $mensagem];
                } else {
                    http_response_code(400);
                    return ['status' => 'error', 'message' => $mensagem];
                }

            } catch (Exception $e) {
                http_response_code(500);
                return [
                    'status' => 'error',
                    'message' => 'Erro no servidor: ' . $e->getMessage()
                ];
            }
        }

        public function deleteContact(){
            // echo 'ENTROU AQUI'
            return $this->delete();
        }

        private function search(){}

        public function searchContact(){}

        public static function closeConnection() {
            if (self::$connection) {
                self::$connection->close();
                self::$connection = null;
            }
        }

        public function jsonSerialize(): mixed {
            return [
                'user' => $this->user,
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'category' => $this->category->value,
                'phone' => $this->phone,
                'notes' => $this->notes
            ];
        }
    }
?>