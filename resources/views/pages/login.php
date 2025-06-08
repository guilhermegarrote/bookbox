<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/bookbox/public/css/style.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sancreek&family=Sedgwick+Ave+Display&family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap');

        .login-erro {
            color: red;
            font-size: 0.9rem;
            margin-top: 10px;
            display: block;
        }
    </style>
</head>

<body class="login-container">
    <div class="login-box">
        <div class="login-form">
            <h2 class="font-urbanist">Login</h2>
            <form id="login-form">
                <label class="login-label font-urbanist">Email:</label>
                <input type="email" name="email" class="login-input font-urbanist" required>

                <label class="login-label font-urbanist">Senha:</label>
                <input type="password" name="senha" class="login-input font-urbanist" required>

                <div id="men-erro" class="login-erro font-urbanist"></div>

                <a class='esqueceu-senha' href="/bookbox/pages/redefinicao-senha/email.php">Esqueceu sua Senha?</a>

                <button type="submit" class="login-button">→</button>
            </form>
        </div>

        <div class="login-right">
            <div class="login-logo font-urbanist"><img src="/bookbox/public/images/logo.png" alt="logo"></div>
            <h1>Bem-vindo</h1>
            <p>ao sistema da biblioteca!</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/bookbox/public/js/login.js"></script>
</body>

</html>
