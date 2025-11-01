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
    <title>Agenda de Contatos</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Inter', sans-serif;
            padding: 30px;
            position: relative;
        }

        h1 {
            color: #1f2937;
            text-align: center;
            margin-bottom: 30px;
        }

        .card {
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-3px);
        }

        .card-header {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .btn-add {
            margin-bottom: 8px;
        }

        table {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        th,
        td {
            vertical-align: middle;
        }

        th i {
            margin: 0 10px;
        }

        tr td span {
            margin: 0 10px;
        }

        #btn-sair {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        /* ===== RESPONSIVIDADE ===== */
        @media (max-width: 992px) {
            .table-responsive {
                font-size: 0.95rem;
            }

            .btn {
                padding: 10px 14px;
                font-size: 0.9rem;
            }
        }

        .btn {
            padding: 10px 15px;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 1.8rem;
            }

            .table-responsive table th,
            .table-responsive table td {
                font-size: 0.85rem;
                padding: 8px 10px;
            }

            .btn-add {
                width: 100%;
                margin-bottom: 15px;
            }

            .actions {
                flex-direction: column;
                gap: 10px;
            }

            input {
                padding: 12px 14px 12px 36px;
                font-size: 0.9rem;
            }

            .contact-info {
                font-size: 0.95rem;
                gap: 12px;
            }
        }

        /* Estilo para input de pesquisa */
        #search-input {
            border-radius: 8px 0 0 8px;
        }

        #btn-search {
            border-radius: 0 8px 8px 0;
        }
    </style>
</head>

<body id="page-contact">
    <a href="" id="btn-sair" class="btn btn-secondary">Sair</a>
    <div class="container">
        <h1>Minha Agenda de Contatos</h1>

        <div class="d-flex mb-3 align-items-center gap-2 flex-wrap">
            <a href="add_contact.php" class="btn btn-primary btn-add"><i class="bi bi-plus-lg"></i> Adicionar Contato</a>

            <!-- Barra de pesquisa -->
            <div class="input-group" style="max-width: 300px;">
                <input type="text" class="form-control" id="search-input" placeholder="Pesquisar contato...">
                <button class="btn btn-outline-secondary" id="btn-search"><i class="bi bi-search"></i> Pesquisar</button>
            </div>
        </div>

        <div class="table-responsive" id="tb1">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><i class="bi bi-person"></i> Nome</th>
                        <th><i class="bi bi-envelope"></i> E-mail</th>
                        <th><i class="bi bi-telephone"></i> Telefone</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="contacts-list">
                    <tr>
                        <td colspan="4" class="text-center text-muted">Carregando contatos...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/contacts.js" defer></script>
</body>

</html>
