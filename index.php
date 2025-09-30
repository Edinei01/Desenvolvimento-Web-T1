<?php
session_start(); // inicia ou retoma a sessão

// Se o usuário já estiver logado, redireciona para a home
if (isset($_SESSION['user'])) {
    header('Location: pages/teste.html');
    exit;
}

// Caso contrário, vai para a tela de login
header('Location: pages/login.html');
exit;
?>