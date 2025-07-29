<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Confirmação de Código</title>

      @vite(['resources/css/pages/recovery-code-validation.css'])
    @routes
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="auth-container">
    <div class="auth-box">
        <form class="auth-form" id="recovery-code-validation-form">
            <h2>Digite o Código</h2>
            <h3 class="verification-subtitle">Ele chegará no seu e-mail</h3>

            <div class="verification-code-container">
                @for ($i = 1; $i <= 6; $i++)
                    <input type="text" name="code[]" maxlength="1" class="verification-input" required inputmode="numeric">

                    @endfor
            </div>

            <button type="submit" class="auth-button">Validar Código</button>

            <button type="button" class="btn-link-style" id="resend-code-btn">
                <span class="btn-link-text">Reenviar código</span>
            </button>
        </form>
    </div>

    @vite('resources/js/pages/auth/recovery-code-validation.js')
</body>

</html>
