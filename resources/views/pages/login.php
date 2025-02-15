<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>

<body>
    <h2>Login</h2>
    
    <!--Verifica se o login deu errado, caso tenha dado vai mostrar essa mensagem, caso você queira fazer algo por exemplo mudar o contorno da label quando der errado o login. Então caso queira fazer algo quando der errado só fazer dentro desse if--> 
    <?php
    if (isset($_SESSION['erro_login'])) {
        echo '<p style="color: red;">' . $_SESSION['erro_login'] . '</p>';
        unset($_SESSION['erro_login']);
    }
    ?>

    <!--<form action="login" method="POST"> faz que quando o botão for clicado ele faça o metodo post que executa la no controller o login--> 
    <!-- mantenha os mesmos name=".." pois é a referência para pegar esses dados lá no back-->
    <form action="login" method="POST">
        <label>Usuário:</label>
        <input type="text" name="usuNome" required>

        <label>Senha:</label>
        <input type="password" name="usuSenha" required>

        <button type="submit">Entrar</button>
    </form>
</body>

</html>