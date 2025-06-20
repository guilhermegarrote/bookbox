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
    <div class="email-box {" style="height: 370px;">
        <div class="email-form">
            <h2>Nova Senha</h2>
            <form id="email-form">
                    <label class="email-label">Senha</label>
                    <input type="password" name="email" class="email-input" required>
                    <div id="erro-email" class="email-erro"></div>

                    <label class="email-label">Comfirma senha</label>
                    <input type="password" name="email" class="email-input" required>
                    <div id="erro-email" class="email-erro"></div>

                <button type="submit" class="nova-senha-button" href="/bookbox/pages/login.php">→</button>
            </form>
        </div>
    </div>

    <script src="/bookbox/public/js/cadastro.js"></script>
</body>

</html>