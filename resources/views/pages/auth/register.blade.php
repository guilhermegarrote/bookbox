<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registrar | Bookbox</title>

    @routes
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="auth-container">
    <div class="auth-box">
        <form id="register-form" class="auth-form">
            <h2>Registrar</h2>

            <x-auth.input label="Nome e sobrenome" name="name" type="text" title="Digite seu nome completo" />
            <x-auth.input label="Email" name="email" type="email" title="Digite seu endereço de e-mail válido" />
            <x-auth.input-password label="Senha" name="password" autocomplete="off" title="Crie uma senha segura" />
            <x-auth.input-password label="Confirmar senha" name="password_confirmation" autocomplete="off"
                title="Confirme sua senha" />

            <button type="submit" id="submit-button" class="auth-button" title="Finalizar registro">→</button>
        </form>
    </div>

    @vite('resources/js/pages/auth/register.js')
</body>

</html>
