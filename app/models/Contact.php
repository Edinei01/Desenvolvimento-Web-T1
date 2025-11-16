<?php 

    namespace app\models;

    require_once __DIR__ . '/../models/User.php';
    require_once __DIR__ . '/../enums/Category.php';
    require_once __DIR__ . '/../core/Database.php';

    use app\models\User;
    use app\enums\Category;
    use app\core\Database;
    use Exception;
    use PDO;
    use PDOException;

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

            $sql = "SELECT * FROM TB_CONTACTS WHERE EMAIL = :email";
            $stmt = self::$connection->prepare($sql);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->execute();
            
            // $result = $stmt->get_result();

            // $data = $result->fetch_assoc();

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                
                $stmt->close();
                return $data;
            }

            // $stmt->close();
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
            $sql = "CALL get_user_contacts(:id)";
            $stmt = self::$connection->prepare($sql);
            $stmt->bindParam(":id", $user_id);
            $stmt->execute();
            // $result = $stmt->get_result();

            $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // while ($row = $result->fetch_assoc()) {
            //     $contacts[] = $row;
            // }

            echo json_encode([
                "status" => "success",
                "data" => $contacts
            ]);

            // $stmt->close();
            // self::$connection::closeConnection();
        }

        public function getContact(int $id_contact): ?array {
            // return $this->getContacts($id_contact);
            return $this->getContactById($id_contact);
        }

        public function getContactID(): ?int{
            
            $email = $this->email;
            $sql = "SELECT ID FROM TB_CONTACTS WHERE EMAIL = :email";
            $stmt = self::$connection->prepare($sql);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->execute();
            $id = $stmt->fetchColumn();
            // $stmt->bind_result($id);
            // $stmt->fetch();
            // $stmt->close();
             
            return $id ?: null;
        }

        private function add(){

            $userId = $this->user->getId();
            $name = $this->name;
            $email = $this->email;
            $phone = $this->phone;
            $categoryValue = $this->category->value;
            $notes = $this->notes;
            
            $sql = "CALL insert_contact(:id, :name, :email, :phone, :categoryValue, :notes, @p_id, @p_success)";
            $stmt = self::$connection->prepare($sql);
            if (!$stmt) {
                http_response_code(500);
                echo json_encode([
                    "status" => "error",
                    "message" => "Erro ao preparar statement"
                ]);
                exit;
            }

            // Vincula os parâmetros à procedure
            $stmt->bindParam(":id", $userId, PDO::PARAM_INT);
            $stmt->bindParam(":name", $name, PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->bindParam(":phone", $phone, PDO::PARAM_STR);
            $stmt->bindParam(":categoryValue", $categoryValue, PDO::PARAM_STR);
            $stmt->bindParam(":notes", $notes, PDO::PARAM_STR);

            // Executa a procedure
            if (!$stmt->execute()) {
                http_response_code(500);
                
                echo json_encode([
                    "status" => "error",
                    "message" => "Erro ao executar procedure: "
                ]);
                // $stmt->close();
                // self::$connection->close();
                exit;
            }
            $stmt->closeCursor();
            // $stmt->close();

            $sql = "SELECT @p_id AS contact_id, @p_success AS success";
            // $row = self::$connection->query($sql);

            $stmt2 = self::$connection->query($sql);
            $row = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                if ($row["success"]) {
                    $this->id = (int)$row["contact_id"];

                    http_response_code(201);
                    echo json_encode([
                    "status" => "success",
                    "message" => "Contato adicionado com sucesso.",
                    "contact_id" => $this->id
                    ]);
                } else {
                    http_response_code(500);
                    echo json_encode([
                    "status" => "error",
                    "message" => "Falha ao inserir contato."
                    ]);
                }
            } else {
                http_response_code(500);
                echo json_encode([
                "status" => "error",
                "message" => "Erro ao recuperar saída da procedure."
                ]);
            }
            // Fecha conexão com o banco
            // self::closeConnection();
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
        
            $sql = "SELECT * FROM TB_CONTACTS WHERE ID = :id";
            $stmt = self::$connection->prepare($sql);
            $stmt->bindParam(":id", $contactId, PDO::PARAM_INT);
            $stmt->execute();
            // $result = $stmt->get_result();

            // $contact = $result->fetch_assoc() ?: null;
            $contact = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

            // $stmt->close();
            return $contact;
        }

        private function list(): array{

            // session_start();
            header('Content-Type: application/json');

            // Busca ID do usuário logado
            // $email = $this->user->getEmailAddress();
            // $sql = "SELECT ID FROM TB_USER WHERE EMAIL = ?";
            // $stmt = self::$connection->prepare($sql);
            // $stmt->bind_param("s", $email);
            // $stmt->execute();
            
            // $user_id = null;
            // $stmt->bind_result($user_id);
            // $stmt->fetch();
            // $stmt->close();

            $email = $this->user->getEmailAddress();
            $sql = "SELECT ID FROM TB_USER WHERE EMAIL = :email";
            $stmt = self::$connection->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            
            $user_id = null;
            $user_id = $stmt->fetchColumn();
            // $stmt->fetch();
            // $stmt = null;

            if (!$user_id) {
                echo json_encode(["status" => "error", "message" => "Usuário não encontrado"], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                exit;
            }

            // Chama a procedure para listar contatos
            $stmt = self::$connection->prepare("CALL get_user_contacts(:id)");
            $stmt->bindParam(":id", $user_id, PDO::PARAM_INT);
            $stmt->execute();
            // $result = $stmt->get_result();

            $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // while ($row = $result->fetch_assoc()) {
            //     $contacts[] = $row;
            // }

            return [
                "status" => "success",
                "data" => $contacts
            ];

            // $stmt->close();
            // self::closeConnection();
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
            $sql = "CALL update_contact(:id, :name, :email, :phone, :category, :notes, @success)";
            $stmt = self::$connection->prepare($sql);
            $stmt->bindParam(":id", $contactId, PDO::PARAM_INT);
            $stmt->bindParam(":name", $name, PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->bindParam(":phone", $phone, PDO::PARAM_STR);
            $stmt->bindParam(":category", $category, PDO::PARAM_STR);
            $stmt->bindParam(":notes", $notes, PDO::PARAM_STR);

            if (!$stmt->execute()) {
                echo json_encode(['status' => 'error', 'message' => 'Falha ao atualizar contato: '], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                // $stmt->close();
                // self::$connection->close();
                exit;
            }

            // $stmt->close();
            $stmt->closeCursor();

            // Recupera valor da variável de saída @success
            $sql = "SELECT @success AS success";
            $stmt = self::$connection->query($sql);
            // $result = self::$connection->query($sql);
            // $row = $result->fetch_assoc();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $success = $row['success'] ?? 0;

            if ($success) {
                return ['status' => 'success', 'message' => 'Contato atualizado com sucesso'];
            } else {
                return ['status' => 'error', 'message' => 'Falha ao atualizar contato'];
            }

            // self::$connection->close();
        }

        public function updateContact(){
            return json_encode($this->update(),JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        private function delete(){

            header('Content-Type: application/json; charset=UTF-8');

            $contactId = $this->id;

            try {
                //  Prepara a chamada da procedure com parâmetro de saída
                $sql = "CALL delete_contact(:id, @resultado)";
                $stmt =  self::$connection->prepare($sql);
                if (!$stmt) {
                    // throw new Exception('Erro ao preparar statement: ' . self::$connection->error);
                    throw new PDOException('Erro ao preparar statement: ' . self::$connection->getMessage());
                }

                $stmt->bindParam(":id", $contactId, PDO::PARAM_INT);
                if (!$stmt->execute()) {
                    throw new PDOException('Erro ao executar statement: ' . $stmt->getMessage());
                }
                // $stmt->close();
                $stmt->closeCursor();

                //  Busca o valor do parâmetro de saída
                $sql = "SELECT @resultado AS resultado";
                $result = self::$connection->query($sql);
                if (!$result) {
                    throw new PDOException('Erro ao buscar resultado: ' . self::$connection->getMessage());
                }

                // $row = $result->fetch_assoc();
                $row = $result->fetch(PDO::FETCH_ASSOC);
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

    // $user = new User();
    // $user = User::loadByEmail("edinei@email.com");
    // $user = User::loadByID(1);
    // echo json_encode($user);
    // $constact = new Contact();
    // // $constact->setUser()
    // var_dump($constact);
?>