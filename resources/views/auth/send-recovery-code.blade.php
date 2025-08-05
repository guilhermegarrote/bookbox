<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Recuperação de Senha</title>

    @routes
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="auth-container">
    <div class="auth-box">
        <form class="auth-form" id="send-recovery-code-form">
            <h2>Recuperação de Senha</h2>

            <x-auth.input label="Email" name="email" type="email" />

            <button type="submit" id="submit-button" class="auth-button">Enviar código</button>
        </form>
    </div>

    @vite('resources/js/pages/auth/send-recovery-code.js')
</body>

</html>
