<?php
    // include_once "../includes/auth/check_session.php";

    // $contactId = intval($_GET['id'] ?? 0);
    // if ($contactId <= 0) {
    //     die("ID de contato inválido.");
    // }

    
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
    <style>
        body { background-color: #f3f4f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; font-family: 'Inter', sans-serif; }
        .container { background: #fff; padding: 40px 30px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); max-width: 500px; width: 100%; }
        h1 { text-align: center; margin-bottom: 30px; font-size: 2rem; font-weight: 700; color: #1f2937; }
        .form-group { position: relative; margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 6px; color: #4b5563; }
        .form-group .bi { position: absolute; left: 14px; top: calc(50% + 12px); transform: translateY(-50%); font-size: 1.2rem; color: #9ca3af; pointer-events: none; }
        input, select, textarea { width: 100%; padding: 14px 16px 14px 42px; border-radius: 12px; border: 1px solid #d1d5db; outline: none; font-size: 1rem; }
        textarea { min-height: 100px; resize: vertical; }
        .actions { display: flex; gap: 12px; margin-top: 20px; }
        .btn { flex: 1; padding: 14px; border-radius: 12px; font-weight: 600; font-size: 1rem; }
    </style>
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
