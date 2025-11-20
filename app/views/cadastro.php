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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/assets/css/reset.css">
    <link rel="stylesheet" href="../../public/assets/css/cadastroUser.css">
    <title>Cadastro</title>
</head>
<body>
    <main>
        <section class="cadastro-section">
            <h2>Cadastro</h2>
            <form id="cadastro-form" method="post">
                <div class="form-group">
                    <label for="name">Usuário:</label>
                    <input type="text" id="name" name="name" required placeholder="Seu nome">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required placeholder="nome@email.com" autocomplete="email">
                </div>
                <div class="form-group">
                    <label for="password">Senha:</label>
                    <input type="password" id="password" name="password" required placeholder="Sua senha" autocomplete="current-password">
                </div>
                <span id="cadastro-error" class="hidden"></span>
                <button type="submit" id="btn-cadastro" class="btn-cadastro">Cadastrar</button>
                <span>Já possui uma conta? <a id="link-cadastrar" class="link-cadastrar-login" href="login.php">Conecte-se</a></span>
            </form>
        </section>
    </main>
    <script src="../../public/assets/js/cadastroUser.js" defer></script>
</body>
</html>