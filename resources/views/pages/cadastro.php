<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>

    <link rel="stylesheet" href="/bookbox/public/css/style.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sancreek&family=Sedgwick+Ave+Display&family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap');
    </style>
</head>

<body class="cadastro-container">
    <div class="cadastro-box">
        <!-- 🔹 Formulário de cadastro -->
        <div class="cadastro-form">
            <h2>Cadastro</h2>

            <form action="cadastro" method="POST">
                <label class="cadastro-label">Nome e sobrenome:</label>
                <input type="text" name="nome" class="cadastro-input" required>

                <label class="cadastro-label">Email:</label>
                <input type="email" name="email" class="cadastro-input" required>

                <label class="cadastro-label">Senha:</label>
                <input type="password" name="senha" class="cadastro-input" required>

                <label class="cadastro-label">Confirmar senha:</label>
                <input type="password" name="senhaConfirmada" class="cadastro-input" required>

                <button type="submit" class="cadastro-button">→</button>
            </form>
        </div>
    </div>
</body>

</html>
