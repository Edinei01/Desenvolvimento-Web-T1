<?php
    include_once '../config/Database.php';
    header('Content-Type: application/json');

    $input = json_decode(file_get_contents('php://input'), true);
    

    // Verifica se recebeu email e senha
    if (isset($input['email']) && isset($input['password'])) {
        $email = trim($input['email']);
        $password = trim($input['password']);

        // Validação simples
        if ($email !== 'admin@teste.com') {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => 'E-mail inválido.',
                'invalid_field' => 'email',
                'http_code' => 401
            ]);
            exit;
        }

        if ($password !== '123456') {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'error' => 'Senha incorreta.',
                'invalid_field' => 'password',
                'http_code' => 401
            ]);
            exit;
        }

        session_start(); // inicia a sessão
        $_SESSION['user'] = [
            'email' => $email,
            'login_time' => time()
        ];

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Login realizado com sucesso!',
            'session_id' => session_id(), // retorna o ID da sessão
            'invalid_field' => null,
            'http_code' => 200
        ]);

    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Dados incompletos.',
            'invalid_field' => null,
            'http_code' => 400
        ]);
    }
    exit;
?>