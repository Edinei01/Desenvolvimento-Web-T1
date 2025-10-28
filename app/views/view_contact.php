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
    <title>Detalhes do Contato</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            background: #fff;
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            max-width: 500px;
            width: 100%;
            transition: transform 0.2s ease;
        }

        .container:hover {
            transform: translateY(-3px);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
            color: #1f2937;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 18px;
            font-size: 1.05rem;
            color: #374151;
        }

        .contact-info div {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            background: #f9fafb;
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .contact-info i {
            color: #6366f1;
            font-size: 1.2rem;
        }

        .btn-back {
            display: block;
            margin: 25px auto 0;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 600;
            text-align: center;
        }

        /* ===== RESPONSIVIDADE ===== */
        @media (max-width: 768px) {
            h1 {
                font-size: 1.8rem;
            }

            .contact-info {
                font-size: 0.95rem;
                gap: 12px;
            }

            .contact-info div {
                padding: 8px 12px;
            }

            .btn-back {
                width: 100%;
            }
        }
    </style>
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
