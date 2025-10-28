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
     * Executa a inserção do contato
     */
    private function add() {
        // Extrai os dados do JSON
        $name     = trim($this->input['name'] ?? '');
        $email    = trim($this->input['email'] ?? '');
        $phone    = trim($this->input['phone'] ?? '');
        $category = trim($this->input['category'] ?? 'Outros');
        $notes    = trim($this->input['notes'] ?? '');

        // Validação básica
        if ($name === '') {
            $this->sendResponse([
                'status' => 'error',
                'message' => "O campo 'name' é obrigatório."
            ]);
            return;
        }

        if ($email === '') {
            $this->sendResponse([
                'status' => 'error',
                'message' => "O campo 'email' é obrigatório."
            ]);
            return;
        }

        if ($phone === '') {
            $this->sendResponse([
                'status' => 'error',
                'message' => "O campo 'phone' é obrigatório."
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

        // Chama o método da classe Contact para adicionar
        $this->contact->addContact($user, $name, $email, $category, $phone, $notes);
    }

    /**
     * Envia JSON formatado
     */
    private function sendResponse(array $data) {
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public static function start() {
        $controller = new self();
        $controller->add(); // Só chama a inserção
    }
}

// Inicializa o controller
ContactController::start();
