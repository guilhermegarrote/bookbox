<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>{{ $email_title ?? 'Notificação | BookBox' }}</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f5f5f5;
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        table {
            border-collapse: collapse !important;
        }

        .email-wrapper {
            width: 100%;
            background: #f5f5f5;
            padding: 20px 0;
        }

        .email-container {
            width: 600px;
            max-width: 600px;
            margin: 0 auto;
            background: #333333;
            border-radius: 6px;
            overflow: hidden;
            padding: 30px;
            color: #ffffff;
        }

        .email-logo {
            display: block;
            margin: 0 auto 20px;
        }

        .email-title {
            color: #ffffff;
            margin: 0 0 25px;
            font-size: 24px;
            font-weight: 600;
            text-align: center;
        }

        .email-text {
            color: #ffffff;
            font-size: 16px;
            margin: 15px 0;
        }

        ul {
            padding-left: 18px;
            margin: 10px 0;
            color: #ffffff;
        }

        .email-alert {
            background-color: #fde2e2;
            border-left: 5px solid #e74c3c;
            padding: 12px 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #333333;
            font-size: 15px;
        }

        .email-highlight {
            background-color: #e0f7e9;
            padding: 12px 15px;
            border-left: 5px solid #27ae60;
            border-radius: 4px;
            margin: 20px 0;
            color: #2c3e50;
            font-size: 15px;
        }

        .email-code {
            background: #ffffff;
            color: #333333;
            font-size: 28px;
            font-weight: bold;
            padding: 12px 24px;
            border-radius: 6px;
            margin: 15px auto;
            display: inline-block;
            letter-spacing: 2px;
        }

        .divider {
            border: none;
            border-top: 1px solid #555555;
            margin: 30px 0;
        }

        .footer-text {
            color: #cccccc;
            font-size: 14px;
            text-align: center;
        }

        .footer-copy {
            color: #888888;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>

<body>
    <table role="presentation" width="100%" class="email-wrapper">
        <tr>
            <td align="center">
                <table role="presentation" class="email-container">
                    <tr>
                        <td align="center">
                            <img src="cid:logo_cid" alt="BookBox Logo" width="150" class="email-logo">

                            <h1 class="email-title">{{ $email_title ?? 'Notificação' }}</h1>

                            @yield('content')

                            <p class="email-text-small" style="color:#cccccc; text-align:center;">
                                Em caso de dúvidas, entre em contato com a biblioteca.
                            </p>

                            <hr class="divider">

                            <p class="footer-text">Atenciosamente, Equipe BookBox</p>
                            <p class="footer-copy">© 2025 BookBox. Todos os direitos reservados.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
