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

<body class="codigo-container">
    <div class="codigo-box {">
        <div class="codigo-form">
            <h2>Digite o Código</h2>
            <h3 class="hc">Ele chegara no seu e-mail</h3>
            <form id=" codigo-form">
                 <div class="codigo-q">
                    <input type="codigo" name="codigo" class="codigo-input" required>
                    <input type="codigo" name="codigo" class="codigo-input" required>
                    <input type="codigo" name="codigo" class="codigo-input" required>
                    <input type="codigo" name="codigo" class="codigo-input" required>
                    <input type="codigo" name="codigo" class="codigo-input" required>
                    <input type="codigo" name="codigo" class="codigo-input" required>
                    <div id="erro-codigo" class="codigo-erro"></div>
                    
                  </div>
                <a class='reenviar' href="/bookbox/pages/redefinicao-senha/email.php">Reenviar</a>
            </form>
        </div>
    </div>

    <script src="/bookbox/public/js/cadastro.js"></script>
</body>

</html>