<?php
// =====================
// 1️⃣ Conexão com o banco
// =====================
$servername = "localhost";
$username_db = "root";
$password_db = "";
$database = "agendaWeb";

$conn = new mysqli($servername, $username_db, $password_db, $database);

// Verifica erro de conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// =====================
// 2️⃣ Busca um usuário do banco
// =====================
$sql = "SELECT NAME, EMAIL FROM TB_USER WHERE ID = 1"; // ← muda o ID se quiser outro
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("Usuário não encontrado!");
}

// =====================
// 3️⃣ Lê o HTML
// =====================
$html = file_get_contents('teste.html');

// =====================
// 4️⃣ Dados dinâmicos (vindos do BD)
// =====================
$title = "Página Inicial";
$username = $user['NAME'];
$email = $user['EMAIL'];

// =====================
// 5️⃣ Substitui os placeholders do HTML
// =====================
$html = str_replace("{{title}}", $title, $html);
$html = str_replace("{{username}}", $username, $html);
$html = str_replace("{{email}}", $email, $html);

// =====================
// 6️⃣ Mostra o HTML final
// =====================
echo $html;

// Fecha a conexão
$conn->close();
?>
