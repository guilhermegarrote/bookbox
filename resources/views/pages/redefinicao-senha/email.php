<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>

    <link rel="stylesheet" href="/bookbox/public/css/style.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sancreek&family=Sedgwick+Ave+Display&family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap');

        .cadastro-erro {
            color: red;
            font-size: 0.9rem;

        }
    </style>
</head>

<body class="email-container">
    <div class="email-box">
        <div class="email-form">
            <h2>Cadastro</h2>
            <form id="email-form">
                <label class="email-label">Email:</label>
                <input type="email" name="email" class="email-input" required>
                <div id="erro-email" class="email-erro"></div>

                <button type="submit" class="email-button" href="pages/redefinicao-senha/codigo.php">Enviar código
                </button>
            </form>
        </div>
    </div>

    <script src="/bookbox/public/js/cadastro.js"></script>
</body>

</html>