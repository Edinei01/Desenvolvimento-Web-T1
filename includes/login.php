<?php
    include_once '../config/Database.php';
    header('Content-Type: application/json');

    // Pega o JSON enviado pelo JS
    $json = file_get_contents('php://input');
    $input = json_decode($json, true);

    // Valida ação
    if (!isset($input['action']) || $input['action'] !== 'login') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit;
    }

    // Valida campos obrigatórios
    if (!isset($input['email']) || !isset($input['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Email and password are required']);
        exit;
    }

    $email = $input['email'];
    $password = $input['password'];

    // Chama a função no MySQL
    $sql = "SELECT check_login_func(?, ?) AS login_status";
    $stmt = $conn->stmt_init();
    $stmt->prepare($sql);
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    $status = $row['login_status'];

    // Retorna JSON de acordo com o resultado
    if ($status === 'ok') {
        echo json_encode([
            'status' => 'success',
            'message' => 'Login successful',
            'invalid_field' => null
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid username or password',
            'invalid_field' => $status // 'email' ou 'password'
        ]);
    }
?>