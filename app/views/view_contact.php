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
    <title>Detalhes do Contato</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/viewContact.css">
</head>

<body id="page-view">
    <div class="container">
        <h1>Detalhes do Contato</h1>
        <div id="contact-info" class="contact-info" data-contact-id="<?= $contactId ?>">
            <div id="name"><i class="bi bi-person"></i> <span class="contact-text"></span></div>
            <div id="email"><i class="bi bi-envelope"></i> <span class="contact-text"></span></div>
            <div id="phone"><i class="bi bi-telephone"></i> <span class="contact-text"></span></div>
            <div id="category"><i class="bi bi-tags"></i> <span class="contact-text"></span></div>
            <div id="notes"><i class="bi bi-journal-text"></i> <span class="contact-text"></span></div>
        </div>
        <a href="contacts.php" class="btn btn-secondary btn-back"><i class="bi bi-arrow-left"></i> Voltar</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./../../public/assets/js/contacts.js" defer></script>
</body>

</html>
