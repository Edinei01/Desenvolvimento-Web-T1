<?php
    session_start();

    // // Limpa todas as variáveis de sessão
    // $_SESSION = [];

    // // Deleta o cookie de sessão, se existir
    // if (ini_get("session.use_cookies")) {
    //     $params = session_get_cookie_params();
    //     setcookie(
    //         session_name(),
    //         '',
    //         time() - 42000, // expira no passado
    //         $params["path"],
    //         $params["domain"],
    //         $params["secure"],
    //         $params["httponly"]
    //     );
    // }

    // // Destrói a sessão
    // session_destroy();

    // header('Location: ./pages/login.html');
    // header('Location: ../app/views/login.html');
     if (isset($_SESSION['user'])) {
        header("Location: contacts.php");
        exit;
    }
    
    header('Location: ../app/views/login.php');
    exit();
?>