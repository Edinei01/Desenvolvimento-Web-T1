<?php
// include_once "../includes/auth/check_session.php";
    
    require_once __DIR__ . "/../models/User.php";

    use app\models\User;

    User::LoggedIn();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Contato</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/addContact.css">
</head>
<body>
    <div class="container">
        <h1>Adicionar Novo Contato</h1>
        <form id="add-contact-form">
            <div class="form-group">
                <label for="name">Nome</label>
                <i class="bi bi-person"></i>
                <input type="text" id="name" name="name" placeholder="Digite o nome completo" required>
            </div>

            <div class="form-group">
                <label for="email">E-mail</label>
                <i class="bi bi-envelope"></i>
                <input type="email" id="email" name="email" placeholder="Digite o e-mail" required>
            </div>

            <div class="form-group">
                <label for="phone">Telefone</label>
                <i class="bi bi-telephone"></i>
                <input type="tel" id="phone" name="phone" placeholder="(99) 99999-9999" required>
            </div>

            <div class="form-group">
                <label for="category">Categoria</label>
                <i class="bi bi-tags"></i>
                <select id="category" name="category" required>
                    <option value="" disabled selected>Selecione uma categoria</option>
                    <option value="familia">Família</option>
                    <option value="trabalho">Trabalho</option>
                    <option value="amigos">Amigos</option>
                    <option value="cliente">Cliente</option>
                    <option value="fornecedor">Fornecedor</option>
                    <option value="outros">Outros</option>
                </select>
            </div>

            <div class="form-group">
                <label for="notes">Observações</label>
                <i class="bi bi-journal-text"></i>
                <textarea id="notes" name="notes" placeholder="Digite algo sobre este contato..."></textarea>
            </div>

            <div class="actions">
                <button id="btn-add" type="submit" class="btn btn-primary">Adicionar</button>
                <a href="contacts.php" class="btn btn-secondary">Voltar</a>
            </div>
        </form>
        <div class="footer-note">
            Sua agenda de contatos avançada 💼
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/contacts.js" defer></script>
</body>
</html>
