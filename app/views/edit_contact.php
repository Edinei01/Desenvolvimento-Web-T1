<?php
    
    require_once __DIR__ . "/../models/Contact.php";
    require_once __DIR__ . "/../models/User.php";


    use App\Models\Contact;
    use app\models\User;

    User::LoggedIn();

    $contactId = Contact::validateContactId();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Contato</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/editcontact.css">
</head>
<body>
    <div class="container">
        <h1>Editar Contato</h1>
        <form id="edit-contact-form" data-contact-id="<?= $contactId ?>">
            <div class="form-group">
                <label for="name">Nome</label>
                <i class="bi bi-person"></i>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">E-mail</label>
                <i class="bi bi-envelope"></i>
                <input type="email" id="email" name="email">
            </div>
            <div class="form-group">
                <label for="phone">Telefone</label>
                <i class="bi bi-telephone"></i>
                <input type="tel" id="phone" name="phone">
            </div>
            <div class="form-group">
                <label for="category">Categoria</label>
                <i class="bi bi-tags"></i>
                <select id="category" name="category" required>
                    <option value="Família">Família</option>
                    <option value="Trabalho">Trabalho</option>
                    <option value="Amigos">Amigos</option>
                    <option value="Cliente">Cliente</option>
                    <option value="Fornecedor">Fornecedor</option>
                    <option value="Outros">Outros</option>
                </select>
            </div>
            <div class="form-group">
                <label for="notes">Observações</label>
                <i class="bi bi-journal-text"></i>
                <textarea id="notes" name="notes"></textarea>
            </div>
            <div class="actions">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Salvar</button>
                <a href="contacts.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Voltar</a>
            </div>
        </form>
    </div>
    <script src="../../public/assets/js/contacts.js" defer></script>
</body>
</html>
