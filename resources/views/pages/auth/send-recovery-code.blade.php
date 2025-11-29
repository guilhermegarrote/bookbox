<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Recuperar Senha | Bookbox</title>

    @routes
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="auth-container">
    <div class="auth-box">
        <form class="auth-form" id="send-recovery-code-form">
            <h2>Recuperar Senha</h2>

            <x-auth.input label="Email" name="email" type="email"
                title="Digite o email da sua conta para receber o código" />
                
            <button type="submit" id="submit-button" class="auth-button" title="Enviar código de recuperação">Enviar
                código</button>
        </form>
    </div>

    @vite('resources/js/pages/auth/send-recovery-code.js')
</body>

</html>
