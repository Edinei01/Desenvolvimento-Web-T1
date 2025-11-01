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

    <style>
        body {
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }

        .container {
            background-color: #ffffff;
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 500px;
            transition: transform 0.2s ease;
        }

        .container:hover {
            transform: translateY(-3px);
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            text-align: center;
            margin-bottom: 30px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            position: relative;
        }

        .form-group label {
            font-weight: 500;
            font-size: 0.95rem;
            color: #4b5563;
            margin-bottom: 6px;
            display: block;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px 16px 14px 42px;
            border-radius: 12px;
            border: 1px solid #d1d5db;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
            background-color: #fff;
            appearance: none;
        }

        .form-group textarea {
            padding-top: 14px;
            padding-bottom: 14px;
            min-height: 100px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99,102,241,0.2);
        }

        .form-group select option {
            font-size: 0.95rem;
        }

        /* Ícones alinhados */
        .form-group .bi {
            position: absolute;
            left: 14px;
            top: calc(50% + 12px);
            transform: translateY(-50%);
            font-size: 1.2rem;
            color: #9ca3af;
            pointer-events: none;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-top: 10px;
        }

        .btn-primary {
            flex: 1;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.2s ease;
        }

        .btn-secondary {
            flex: 1;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
        }

        .footer-note {
            text-align: center;
            margin-top: 25px;
            font-size: 0.85rem;
            color: #6b7280;
        }

        /* ===== RESPONSIVIDADE ===== */
        @media (max-width: 992px) {
            .table-responsive { font-size: 0.95rem; }
            .btn { padding: 10px 14px; font-size: 0.9rem; }
        }

        @media (max-width: 768px) {
            h1 { font-size: 1.8rem; }
            .table-responsive table th,
            .table-responsive table td { font-size: 0.85rem; padding: 8px 10px; }
            .btn-add { width: 100%; margin-bottom: 15px; }
            .actions { flex-direction: column; gap: 10px; }
            input, select, textarea { padding: 12px 14px 12px 38px; font-size: 0.9rem; }
            .contact-info { font-size: 0.95rem; gap: 12px; }
        }
    </style>
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
