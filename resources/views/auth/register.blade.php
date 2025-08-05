<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registrar</title>

    @routes
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="auth-container">
    <div class="auth-box">
        <form id="register-form" class="auth-form">
            <h2>Registrar</h2>

            <x-auth.input label="Nome e sobrenome" name="name" type="text" />
            <x-auth.input label="Email" name="email" type="email" />
            <x-auth.input-password label="Senha" name="password" />
            <x-auth.input-password label="Confirmar senha" name="password_confirmation" />

            <button type="submit" id="submit-button" class="auth-button">→</button>
        </form>
    </div>

    @vite('resources/js/pages/auth/register.js')
</body>

</html>
