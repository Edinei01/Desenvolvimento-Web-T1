<?php
header('Content-Type: application/json; charset=utf-8');

// só aceitar POST (opcional)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// lê JSON do corpo
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!is_array($data) || !isset($data['action']) || $data['action'] !== 'destroy_session') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Requisição inválida']);
    exit;
}

// inicia sessão (se ainda não iniciada)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// limpa variáveis de sessão
$_SESSION = [];

// destrói cookie de sessão (se existir)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// destrói a sessão
session_destroy();

// responde com JSON dizendo que deu certo
echo json_encode(['success' => true]);
exit;
