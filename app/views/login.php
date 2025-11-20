<?php 
    
    require_once __DIR__ . "/../../config/Config.php";
    require_once __DIR__ . "/../models/User.php";

    use app\models\User;
    use Config\Config;

    session_start();

    if (isset($_SESSION['user'])) {
        header("Location: contacts.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../../public/assets/css/reset.css">
    <link rel="stylesheet" href="../../public/assets/css/login.css">
</head>

<body>
    <main>
        <section class="login-section">
            <h2>Login</h2>
            <form id="login-form" method="post">
                <div class="form-group ">
                    <label for="email">Usuário:</label>
                    <input type="email" id="email" name="email" required placeholder="nome@email.com" autocomplete="email">
                </div>
                <div class="form-group">
                    <label for="password">Senha:</label>
                    <input type="password" id="password" name="password" required placeholder="your password" autocomplete="current-password">
                </div>
                <span id="login-error" class="hidden"></span>
                <button type="submit" class="btn-login">Login</button>
                <span>Ainda não tem uma conta? <a id="link-cadastrar" class="link-cadastrar-login"  href="cadastro.php">Cadastre-se!</a></span>
            </form>
        </section>
    </main>

    <script src="../../public/assets/js/login.js" defer></script>
</body>

</html>