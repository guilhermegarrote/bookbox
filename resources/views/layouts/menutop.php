<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookbox</title>

    <link rel="stylesheet" href="/Bookbox/public/css/style.css">
</head>

<body>

    <header>
        <nav>
            <!-- 🔹 Container da Logo e do Retângulo -->
            <div class="nav-container">
                <img class="logo" src="/Bookbox/public/images/logo.png" alt="logo">

                <!-- 🔹 Retângulo contendo a barra de pesquisa e botões -->
                <div class="retangulo">
                    <input type="text" class="search-box" placeholder="Pesquisar...">
                    <button class="search-btn"><img class="buscar" src="\bookbox\public\images\Filtro.png" alt="buscar"></button>>
                    <button class="nav-btn">Emprestimos</button>
                    <button class="nav-btn">Livros</button>
                    <button class="nav-btn">Alunos</button>
                

                    <!-- 🔹 Quadrado com três pontinhos -->
                    <div class="menu-btn" onclick="toggleMenu()">⋮</div>

                </div>
                
            </div>
        </nav>
    </header>


</body>

</html>
