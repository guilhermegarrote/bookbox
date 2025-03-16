<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/bookbox/public/css/style.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sancreek&family=Sedgwick+Ave+Display&family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap');
    </style>
</head>

<body class="login-container">
    <div class="login-box">
        <!-- 🔹 Lado esquerdo (formulário) -->
        <div class="login-form font-urbanist">
            <h2>Login</h2>
            <!-- Formulário de login -->
            <form action="login" method="POST">
                <label class="login-label font-urbanist">Email:</label>
                <input type="text" name="email" class="login-input font-urbanist" required>

                <label class="login-label font-urbanist">Senha:</label>
                <input type="password" name="senha" class="login-input font-urbanist" required>

                <!-- Mensagem de erro -->
                <?php if (isset($_SESSION['errors'])): ?>
                    <p class="login-error font-urbanist"><?= $_SESSION['errors']; ?></p>
                    <?php unset($_SESSION['errors']); ?>
                <?php endif; ?>

                <button type="submit" class="login-button">→</button>
            </form>
        </div>

        <!-- 🔹 Lado direito (bem-vindo) -->
        <div class="login-right">
            <div class="login-logo font-urbanist"><img src="\bookbox\public\images\logo.png" alt="logo"></div>
            <h1>Bem-vindo</h1>
            <p>ao sistema da biblioteca!</p>
        </div>
    </div>
</body>

</html>