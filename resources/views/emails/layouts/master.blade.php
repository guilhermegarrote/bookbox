<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $email_title ?? 'Notificação | BookBox' }}</title>

    <style>
        :root {
            --dark-background: #121212;
            --light-background: #1e1e1e;
            --light-text: #e1e1e1;
            --logo-color: #ffffff;
            --highlight-color: #1abc9c;
            --highlight-hover: #16a085;
            --highlight-text: #333;
            --highlight-border: #1abc9c;
            --footer-text: #888;
            --footer-copy: #aaa;
            --divider: #333;
            --return-border: #3b8fbb;
            --dark-return-border: #2980b9;
            --footer-text-light: #fff;
            --light-return-border: #fff;
        }

        body,
        h1,
        p,
        ul {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', Arial, sans-serif;
        }

        body {
            background-color: var(--dark-background, #121212);
            color: var(--light-text, #e1e1e1);
            line-height: 1.6;
            font-size: 16px;
        }

        .email-wrapper {
            width: 100%;
            padding: 8% 5%;
            background-color: var(--dark-background, #121212);
            box-sizing: border-box;
        }

        .email-container {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            background-color: var(--light-background, #1e1e1e);
            border-radius: 10px;
            padding: 5%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            box-sizing: border-box;
        }

        .email-logo {
            display: block;
            margin: 0 auto 5%;
            width: 25vw;
            max-width: 140px;
        }

        .email-title {
            font-size: 2rem;
            color: var(--logo-color, #ffffff);
            font-weight: 700;
            text-align: center;
            margin-bottom: 5%;
            letter-spacing: 0.5px;
            line-height: 1.4;
        }

        .email-text {
            font-size: 1rem;
            color: var(--light-text, #e1e1e1);
            margin-bottom: 18px;
            line-height: 1.5;
        }

        ul {
            padding-left: 20px;
            margin-left: 40px;
            margin-bottom: 20px;
        }

        ul li {
            font-size: 1rem;
            margin-bottom: 12px;
            color: var(--light-text, #e1e1e1);
            list-style-position: inside;
        }

        .email-highlight {
            background-color: var(--highlight-text, #333);
            border-left: 5px solid var(--highlight-border, #1abc9c);
            padding: 5% 3%;
            border-radius: 8px;
            font-size: 1rem;
            color: var(--light-text, #e1e1e1);
            margin-bottom: 5%;
        }

        .email-btn {
            background-color: var(--highlight-color, #1abc9c);
            color: #fff;
            padding: 3% 8%;
            text-decoration: none;
            font-size: 1rem;
            border-radius: 8px;
            display: inline-block;
            text-align: center;
            margin-top: 5%;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .email-btn:hover {
            background-color: var(--highlight-hover, #16a085);
            transform: translateY(-2px);
        }

        .footer-text {
            font-size: 1rem;
            color: var(--footer-text, #888);
            text-align: center;
            margin-top: 5%;
        }

        .footer-copy {
            font-size: 0.875rem;
            color: var(--footer-copy, #aaa);
            text-align: center;
        }

        .divider {
            border: none;
            border-top: 1px solid var(--divider, #333);
            margin: 5% 0;
        }

        .highlight-overdue {
            padding: 10px 15px;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
            margin: 15px 0;
            display: inline-block;
            width: fit-content;
            font-size: 1.125rem;
            background-color: #e74c3c;
            color: #fff;
        }

        .highlight-remaining {
            padding: 10px 15px;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
            margin: 15px 0;
            display: inline-block;
            width: fit-content;
            font-size: 1.125rem;
            background-color: #f1c40f;
            color: #fff;
            border: 2px solid #f39c12;
        }

        .highlight-due-date {
            background-color: var(--return-border, #3b8fbb);
            padding: 3% 6%;
            border-radius: 8px;
            font-weight: bold;
            color: var(--footer-text-light, #fff);
            margin: 5% auto 10%;
            display: block;
            width: fit-content;
            border: 2px solid var(--dark-return-border, #2980b9);
            font-size: 1.125rem;
        }

        .due-date {
            font-size: 1.125rem;
            color: var(--footer-text-light, #fff);
            font-weight: bold;
        }

        .highlight-recovery-code {
            display: block;
            background-color: #0b1222;
            color: #1a73e8;
            font-weight: 700;
            font-size: 22px;
            letter-spacing: 4px;
            padding: 12px 20px;
            border-radius: 6px;
            margin: 20px auto;
            text-align: center;
            user-select: all;
        }

        @media screen and (max-width: 900px) {
            .email-title {
                font-size: 1.6rem;
            }

            .email-container {
                padding: 7% 5%;
            }

            .email-logo {
                width: 30vw;
            }

            .email-btn {
                padding: 3% 7%;
                font-size: 1rem;
            }

            .highlight-due-date {
                width: 80%;
                font-size: 1rem;
            }

            .footer-text,
            .footer-copy {
                font-size: 1rem;
            }
        }

        @media screen and (max-width: 600px) {
            .email-title {
                font-size: 1.25rem;
            }

            .email-container {
                padding: 10% 5%;
            }

            .email-logo {
                width: 35vw;
            }

            .email-btn {
                padding: 4% 8%;
                font-size: 1rem;
            }

            .highlight-due-date {
                width: 90%;
                font-size: 1rem;
            }

            .highlight-recovery-code {
                font-size: 18px;
                padding: 10px 16px;
            }

            .footer-text,
            .footer-copy {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-container">
            <img src="cid:logo_cid" alt="BookBox Logo" class="email-logo">

            @yield('content')

            <div class="email-highlight">
                <p><strong>Observação:</strong> Em caso de dúvidas, entre em contato com a biblioteca.</p>
            </div>

            <p class="footer-text">Atenciosamente, Equipe BookBox</p>
            <p class="footer-copy">© 2025 BookBox. Todos os direitos reservados.</p>
        </div>
    </div>
</body>

</html>
