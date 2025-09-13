<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Confirmar Código | Bookbox</title>

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
                    <input type="text" name="code[]" maxlength="1" class="verification-input" required
                        inputmode="numeric" title="Digite o {{ $i }}º dígito do código de verificação">
                @endfor
            </div>

            <button type="submit" class="auth-button" title="Validar código de recuperação">Validar Código</button>

            <button type="button" class="btn-link-style" id="resend-code-btn" title="Reenviar código para o seu e-mail">
                <span class="btn-link-text">Reenviar código</span>
            </button>
        </form>
    </div>

    @vite('resources/js/pages/auth/recovery-code-validation.js')
</body>

</html>
