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
    <link rel="stylesheet" href="../../public/assets/css/contacts.css">
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
