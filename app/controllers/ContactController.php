<?php

    namespace app\Controllers;

    require_once __DIR__ . '/../models/Contact.php';
    require_once __DIR__ . '/../models/User.php';
    require_once __DIR__ . '/../enums/Category.php';

    use app\models\Contact;
    use app\models\User;
    use app\enums\Category;

    class ContactController {
        
        private Contact $contact;
        private array $input;

        public function __construct() {
            header('Content-Type: application/json; charset=utf-8');

            // Pega o JSON enviado pelo fetch
            $this->input = json_decode(file_get_contents('php://input'), true);

            if (!$this->input) {
                $this->sendResponse([
                    'status' => 'error',
                    'message' => 'Nenhum dado recebido ou JSON inválido.'
                ]);
                exit;
            }

            // Inicializa o objeto Contact vazio
            $this->contact = new Contact();
        }

        /**
         * Método principal que decide a ação
         */
        private function handleRequest() {
            // $action = $_GET['action'] ?? ($this->input['action'] ?? '');
            $action = $_GET['action'] ?? ($this->input['action'] ?? '');
            // echo json_encode(['action' => $action, 'id' => $this->input['id']], );
            // exit;
            switch ($action) {
                case 'addContact':
                    $this->addContact();
                    break;
        
                case 'listContacts':
                    $this->listContacts();
                    break;

                case 'deleteContact':
                    $this->deleteContact();
                    break;

                case 'getContact':
                    $this->getContact();
                    break;

                case 'editContact':
                    $this->editContact();
                    break;

                case 'viewContact':
                    // echo json_encode(['action' => $action, 'id' => $this->input['id']]);
                    $this->viewContact();
                    break;

                default:
                    $this->sendResponse([
                        'status' => 'error',
                        'message' => 'Ação inválida ou não especificada.'
                    ]);
            }
        }

        /**
         * Adiciona um novo contato
         */
        private function addContact() {
            $data = $this->input['contact'] ?? [];

            $name     = trim($data['name'] ?? '');
            $email    = trim($data['email'] ?? '');
            $phone    = trim($data['phone'] ?? '');
            $category = trim($data['category'] ?? 'Outros');
            $notes    = trim($data['notes'] ?? '');

            // Validação básica
            if ($name === '' || $email === '' || $phone === '') {
                $this->sendResponse([
                    'status' => 'error',
                    'message' => 'Campos obrigatórios não preenchidos.'
                ]);
                return;
            }

            // Pega o usuário logado via sessão
            session_start();
            if (!isset($_SESSION['user'])) {
                $this->sendResponse([
                    'status' => 'error',
                    'message' => "Usuário não logado."
                ]);
                return;
            }

            $user = User::loadByEmail($_SESSION['user']);
            if (!$user) {
                $this->sendResponse([
                    'status' => 'error',
                    'message' => "Usuário não encontrado!"
                ]);
                return;
            }

            // Chama o método da classe Contact
            $this->contact->addContact($user, $name, $email, $category, $phone, $notes);
        }

        /**
         * Listar contatos
         */
        private function listContacts() {
            session_start();
            if (!isset($_SESSION['user'])) {
                $this->sendResponse([
                    'status' => 'error',
                    'message' => "Usuário não logado."
                ]);
                return;
            }

            $user = User::loadByEmail($_SESSION['user']);

            if (!$user) {
                $this->sendResponse([
                    'status' => 'error',
                    'message' => "Usuário não encontrado!"
                ]);
                return;
            }

            $this->contact = new Contact($user);

            $result = $this->contact->listContact();

        
            $this->sendResponse($result);

        }

        /**
         * Editar contato 
         */
        private function getContact() {


            session_start();
            if (!isset($_SESSION['user'])) {
                $this->sendResponse([
                    'status' => 'error',
                    'message' => "Usuário não logado."
                ]);
                return;
            }

            $user = User::loadByEmail($_SESSION['user']);

            if (!$user) {
                $this->sendResponse([
                    'status' => 'error',
                    'message' => "Usuário não encontrado!"
                ]);
                return;
            }

            $this->contact = new Contact($user);

            $contact = $this->contact->getContact($this->input['id'] ?? 0);

            // echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            // return;
            
            // $this->sendResponse($result);



            // $contact = new Contact();

// $dado = $contact->getContact(10);
            // $this->sendResponse([
            //     'status' => 'success',
            //     'message' => 'Edição de contato ainda não implementada.'
            // ]);

            $this->sendResponse([
                "status" => "success",
                "data" => $contact
            ]);
        }

        /**
         * Editar contato 
         */
        private function editContact() {
            $this->sendResponse([
                'status' => 'success',
                'message' => 'Edição de contato ainda não implementada.'
            ]);
        }

        /**
         * Ver contatos
         */
        private function viewContact() {

            session_start();

            if (!isset($_SESSION['user'])) {
                $this->sendResponse([
                    "status" => "error",
                    "message" => "Usuário não autenticado"
                ]);
                return;
            }

            $id = $this->input['id'] ?? null;
            
            if (!$id) {
                $this->sendResponse([
                    "status" => "error",
                    "message" => "ID do contato não especificado"
                ]);
                return;
            }
            $contact = new Contact();
            // $contact = $this->contact->getContactById($id);
            $contact = $contact->getContact($id);

            $this->sendResponse([
                "status" => "success",
                "data" => $contact
            ]);
        }

        /**
         * Deletar contato (implementação futura)
         */
        private function deleteContact() {
            $this->sendResponse([
                'status' => 'success',
                'message' => 'Remoção de contato ainda não implementada.'
            ]);
        }

        /**
         * Envia JSON formatado
         */
        private function sendResponse(array $data) {
            echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        /**
         * Ponto de entrada
         */
        public static function start() {
            $controller = new self();
            $controller->handleRequest();
        }
    }

    // Inicializa o controller
    ContactController::start();
