<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login | Bookbox</title>

    @routes
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="auth-container">
    <div class="login-box">
        <form class="login-form" id="login-form">
            <h2>Login</h2>

            <x-auth.input label="Email" name="email" type="email" title="Digite seu endereço de e-mail" />
            <x-auth.input-password label="Senha" name="password" title="Digite sua senha de acesso" />

            <a class="auth-link" href="{{ route('recovery.email.form') }}" title="Recuperar acesso à sua conta">
                <span class="auth-link-text">Esqueceu sua senha?</span>
            </a>

            <button type="submit" id="submit-button" class="auth-button" title="Entrar no sistema">→</button>
        </form>

        <div class="login-side">
            <div class="login-logo">
                <img src="{{ asset('images/logo/logo.png') }}" alt="Logo do sistema Bookbox" title="Logo Bookbox">
            </div>
            <h1>Bem-vindo</h1>
            <p>ao sistema da biblioteca!</p>
        </div>
    </div>

    @vite('resources/js/pages/auth/login.js')
</body>

</html>
