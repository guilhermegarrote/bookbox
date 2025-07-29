<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <title>Recuperação de Senha</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9fafb;
            color: #222222;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            background-color: #ffffff;
            margin: 0 auto;
            padding: 30px 40px;
            border: 1px solid #dddddd;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #1a73e8;
            font-weight: 600;
            margin-bottom: 25px;
            font-size: 24px;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
            margin: 15px 0;
        }

        .code {
            display: inline-block;
            background-color: #eef4fb;
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
    </style>
</head>

<body>
    <div class="container">
        <h1>Recuperação de Senha</h1>
        <p>Prezado(a) {{ $email }},</p>
        <p>Recebemos uma solicitação para redefinição da senha associada a este endereço de e-mail.</p>
        <p>Seu código de recuperação de senha é:</p>
        <div class="code">{{ $code }}</div>
        <p>Por favor, utilize este código para proceder com a redefinição da sua senha.</p>
        <p>Se você não solicitou esta recuperação, por favor, desconsidere esta mensagem ou entre em contato com nosso suporte imediatamente.</p>

        <footer>
            <p>Este é um e-mail automático, por favor, não responda.</p>
            <p>&copy; {{ date('Y') }} Sua Empresa. Todos os direitos reservados.</p>
        </footer>
    </div>
</body>

</html>
