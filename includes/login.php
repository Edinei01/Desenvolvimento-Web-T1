<?php
include_once '../Config/Database.php';
header('Content-Type: application/json; charset=utf-8');
session_start();

/**
 * Inserir usuário no banco usando procedure insert_user
 */
function insertUser($conn, string $name, string $email, string $pass): array {
    $conn->query("SET @p_id = 0, @success = FALSE;");
    $stmt = $conn->prepare("CALL insert_user(?, ?, ?, @p_id, @success)");
    $stmt->bind_param("sss", $name, $email, $pass);
    $stmt->execute();
    $stmt->close();

    $result = $conn->query("SELECT @p_id AS id, @success AS success");
    $row = $result->fetch_assoc();

    if ($row['success']) {
        return ['success' => true, 'user_id' => (int)$row['id'], 'message' => 'Usuário inserido com sucesso!'];
    }

    return ['success' => false, 'user_id' => null, 'message' => 'Falha ao inserir usuário.'];
}

/**
 * Verificar login usando procedure check_login
 */
function checkLogin($conn, string $email, string $pass): array {
    $conn->query("SET @p_user_id = NULL, @success = FALSE;");
    $stmt = $conn->prepare("CALL check_login(?, ?, @p_user_id, @success)");
    $stmt->bind_param("ss", $email, $pass);
    $stmt->execute();
    $stmt->close();

    $result = $conn->query("SELECT @p_user_id AS user_id, @success AS success");
    $row = $result->fetch_assoc();

    if ($row['success']) {
        $_SESSION['user'] = [
            'user_id' => (int)$row['user_id'],
            'email' => $email,
            'login_time' => time()
        ];
        return [
            'success' => true,
            'user_id' => (int)$row['user_id'],
            'message' => 'Login realizado com sucesso!',
            'session_id' => session_id()
        ];
    }

    return ['success' => false, 'user_id' => null, 'message' => 'E-mail ou senha incorretos.', 'invalid_field' => 'email'];
}

// 🔹 Recebe dados JSON do cliente
$input = json_decode(file_get_contents('php://input'), true);

// Validar se action existe
if (!isset($input['action'])) {
    echo json_encode(['success' => false, 'message' => 'Ação inválida ou dados incompletos.']);
    exit;
}

// Registrar usuário
if ($input['action'] === 'register') {
    $response = insertUser($conn, $input['name'], $input['email'], $input['password']);
    echo json_encode($response);
    exit;
}

// Login
if ($input['action'] === 'login') {
    if (!isset($input['email'], $input['password'])) {
        echo json_encode(['success' => false, 'message' => 'Email ou senha não enviados.']);
        exit;
    }

    $response = checkLogin($conn, $input['email'], $input['password']);
    echo json_encode($response);
    exit;
}

// Caso action inválida
echo json_encode(['success' => false, 'message' => 'Ação inválida ou dados incompletos.']);
exit;
?>