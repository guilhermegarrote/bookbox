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

<body class="cadastro-container">
    <div class="cadastro-box">
        <div class="cadastro-form">
            <h2>Cadastro</h2>
            <form id="cadastro-form">
               <label class="cadastro-label">Nome e sobrenome:</label>
                    <input type="text" name="nome" class="cadastro-input" required>
                    <div id="erro-nome" class="cadastro-erro"></div>

                    <label class="cadastro-label">Email:</label>
                    <input type="email" name="email" class="cadastro-input" required>
                    <div id="erro-email" class="cadastro-erro"></div>

                    <label class="cadastro-label">Senha:</label>
                    <input type="password" name="senha" class="cadastro-input" required>
                    <div id="erro-senha" class="cadastro-erro"></div>

                    <label class="cadastro-label">Confirmar senha:</label>
                    <input type="password" name="senhaConfirmada" class="cadastro-input" required>
                    <div id="erro-senhaConfirmada" class="cadastro-erro"></div>

                    <!-- Mensagem geral vinda do backend ou erro inesperado -->
                    <div id="mens-erro" class="cadastro-erro font-urbanist"></div>

                <button type="submit" class="cadastro-button">→</button>
            </form>
        </div>
    </div>

    <script src="/bookbox/public/js/cadastro.js"></script>
</body>

</html>