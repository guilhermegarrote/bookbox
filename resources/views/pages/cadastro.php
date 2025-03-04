<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>

    <link rel="stylesheet" href="/bookbox/public/css/style.css">
</head>

<body>
    <h2>Cadastro</h2>

    <form action="cadastro" method="POST">
        <label>Nome e sobrenome:</label>
        <input type="text" name="nome" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Senha:</label>
        <input type="password" name="senha" required>

        <label>Confirmar senha:</label>
        <input type="password" name="senhaConfirmada" required>

        <button type="submit">Entrar</button>
    </form>
</body>

</html>