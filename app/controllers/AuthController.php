<?php 

    require_once __DIR__ . '/../models/Auth.php';
    require_once __DIR__ . '/../models/User.php';
    
    use app\models\Auth;
    use app\models\User;

    class AuthController {

        private $user;
        private $input;

        public function __construct() {
            header('Content-Type: application/json; charset=utf-8');

            // Pega o JSON recebido
            $this->input = json_decode(file_get_contents('php://input'), true);
            // Cria instância do User com dados (se existirem)
            $this->user = new User(
                $this->input['name'] ?? '',
                $this->input['email'] ?? '',
                $this->input['password'] ?? ''
            );
        }

        private function handleRequest() {
            $action = $this->input['action'] ?? '';
            switch ($action) {
                case 'register':
                    // $this->register();
                break;

                case 'login':
                    
                    $this->login();
                break;
            
                case 'logout':
                    // $this->logout();
                break;
            
                default:
                    $this->sendResponse([
                    'status' => 'error',
                    'message' => 'Ação inválida'
                ]);
            }
        }

        private function sendResponse(array $data) {
            echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        public static function start(){
            $controller = new self();
            $controller->handleRequest();
        }

        public function login(){
            header("Content-Type: application/json");

            $auth = new Auth($this->user->getEmailAddress() ?? "", $this->user->getPassword() ?? "");
            $result = $auth->login();

            echo json_encode($result);
            exit;
        }

        public static function requireLogin() {
            session_start();
            if (!isset($_SESSION["user"])) {
                header("Location: ../../public/index.php");
                exit;
            }
        }
    }
    AuthController::start();
?>