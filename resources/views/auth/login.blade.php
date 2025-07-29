<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login</title>

    @vite(['resources/css/pages/login.css'])
    @routes
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="auth-container">
    <div class="login-box">
        <form class="login-form" id="login-form">
            <h2>Login</h2>

            <x-auth.input label="Email" name="email" type="email" />
            <x-auth.input-password label="Senha" name="password" />

            <a class="auth-link" href="{{ route('recovery.email.form') }}"><span class="auth-link-text">Esqueceu sua senha?</span></a>

            <button type="submit" id="submit-button" class="auth-button">→</button>
        </form>

        <div class="login-side">
            <div class="login-logo">
                <img src="{{ asset('images/logo/logo.png') }}" alt="logo">
            </div>
            <h1>Bem-vindo</h1>
            <p>ao sistema da biblioteca!</p>
        </div>
    </div>

    @vite('resources/js/pages/auth/login.js')
</body>

</html>
