<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <title>Recuperação de Senha</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body style="margin:0; padding:0; background:#f5f5f5; font-family:Arial, sans-serif; line-height:1.6;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f5f5f5; padding:20px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="background:#333333; border-radius:6px; overflow:hidden; padding:30px;">
                    <tr>
                        <td align="center" style="text-align:center;">
                            <!-- Logo embedded -->
                            <img src="cid:logo_cid" alt="Bookbox Logo" width="150" style="display:block; margin:0 auto 20px;" />

                            <!-- Título -->
                            <h1 style="color:#ffffff; margin:0 0 25px; font-size:24px; font-weight:600;">
                                Recuperação de Senha
                            </h1>

                            <!-- Saudação -->
                            <p style="color:#ffffff; font-size:16px; margin:15px 0;">
                                Olá <strong>{{ $name }}</strong>,
                            </p>

                            <p style="color:#ffffff; font-size:16px; margin:15px 0;">
                                Seu código de recuperação é:
                            </p>

                            <!-- Código -->
                            <div style="display:inline-block; background:#ffffff; color:#1a73e8; font-size:22px; font-weight:bold; letter-spacing:3px; padding:12px 24px; border-radius:6px; margin:20px 0; user-select:all;">
                                {{ $code }}
                            </div>

                            <!-- Instruções -->
                            <p style="color:#ffffff; font-size:14px; margin:15px 0; text-align:left;">
                                Digite este código na página de redefinição para criar uma nova senha.
                                Se você não solicitou a recuperação, ignore este e-mail ou entre em contato com nossa equipe de suporte.
                            </p>

                            <!-- Rodapé -->
                            <hr style="border:none; border-top:1px solid #555555; margin:30px 0;" />
                            <p style="color:#cccccc; font-size:13px; margin:0;">
                                Atenciosamente, Equipe BookBox
                            </p>
                            <p style="color:#888888; font-size:12px; margin:5px 0 0;">
                                © 2025 BookBox. Todos os direitos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>