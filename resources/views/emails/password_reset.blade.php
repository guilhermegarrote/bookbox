<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <title>Recuperação de Senha</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffffff;
            color: #f9fafb;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            background-color: #333;
            margin: 0 auto;
            padding: 30px 40px;
            border: 1px solid #000000ff;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #ffffffff;
            font-weight: 600;
            margin-bottom: 25px;
            font-size: 24px;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
            margin: 15px 0;
            color: #ffffffff;
        }

        .code {
            display: inline-block;
            color: #1a73e8;
            font-weight: 700;
            font-size: 22px;
            letter-spacing: 4px;
            padding: 12px 20px;
            border-radius: 6px;
            margin: 20px 0;
            user-select: all;
        }

        footer {
            margin-top: 40px;
            font-size: 13px;
            color: #666666;
            text-align: center;
            border-top: 1px solid #eeeeee;
            padding-top: 15px;
        }

        .logo {
            text-align: center;
            display: block;
            font-size: 42px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="cid:bookbox_logo" alt="Bookbox" width="150" />

        <h1>Recuperação de Senha</h1>
        <p>Olá {{ $userName }},</p>
        <p>Recebemos uma solicitação para redefinir a senha da sua conta no BookBox.</p>
        <p>Seu código de recuperação de senha é:</p>
        <div class="code">{{ $code }}</div>
        <p>Digite este código na página de redefinição para criar uma nova senha.</p>
        <p>
            Se você não solicitou a recuperação, ignore este e-mail ou entre em contato imediatamente com nossa equipe
            de suporte.
        </p>

        <footer>
            <p>Atenciosamente,
                Equipe BookBox.</p>
            <p>&copy; {{ date('Y') }} Sua Empresa. Todos os direitos reservados.</p>
        </footer>
    </div>
</body>

</html>
