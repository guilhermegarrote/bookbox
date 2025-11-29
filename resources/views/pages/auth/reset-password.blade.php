<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Nova Senha | Bookbox</title>

    @routes
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="auth-container">
    <div class="auth-box">
        <form id="reset-password-form" class="auth-form">
            <h2>Nova Senha</h2>

            <x-auth.input-password label="Senha" name="password" title="Digite sua nova senha" />
            <x-auth.input-password label="Confirmar senha" name="password_confirmation" title="Confirme sua nova senha" />

            <button type="submit" id="submit-button" class="auth-button" title="Salvar nova senha">→</button>
        </form>
    </div>

    @vite('resources/js/pages/auth/reset-password.js')
</body>

</html>
