<?php

namespace app\Controllers;
require_once __DIR__ . '/../models/User.php';

use app\models\User;

class UserController {

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

    /**
     * Método principal que decide a ação
     */
    private function handleRequest() {
        $action = $this->input['action'] ?? '';
        switch ($action) {
            case 'register':
                $this->register();
                break;

            case 'login':
                $this->login();
                break;
            
            case 'logout':
                $this->logout();
                break;
            
            default:
                $this->sendResponse([
                    'status' => 'error',
                    'message' => 'Ação inválida'
                ]);
        }
    }

    /**
     * Chama o método de criação de usuário
     */
    private function register() {
        $this->user->registerUser();
    }

    /**
     * Chama o método de login do usuário
     */
    private function login() {
        $this->user->login();
    }

    /**
     * Chama o método de logout do usuário
     */
    private function logout() {
    $response = $this->user->logout();
    $this->sendResponse($response);
}


    /**
     * Método auxiliar para enviar JSON formatado
     */
    private function sendResponse(array $data) {
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public static function start(){
        $controller = new self();
        $controller->handleRequest();
    }
}

UserController::start();