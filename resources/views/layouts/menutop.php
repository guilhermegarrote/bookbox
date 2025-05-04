<header>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sancreek&family=Sedgwick+Ave+Display&family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap');
    </style>
    <nav>
        <div class="menusuperior">
            <img class="logo" src="/bookbox/public/images/logomarca.png" alt="logomarca">

            <div class="retangulo">
                <input type="text" class="barradepesquisa" placeholder="Pesquisar">
                <button class="btnfiltro" id="btn-filtro"><img class="filtrar" src="/bookbox/public/images/filtro.png" alt="filtrar"></button>
                <div id="popupFiltro" style="position: absolute; display: none; background: white; border: 1px solid #ccc; padding: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.2); z-index: 1000; min-width: 150px; border-radius: 4px;"></div>

                <div class="botoes">
                    <button class="menu-top-btn" data-pagina="emprestimos">Empréstimos</button>
                    <button class="menu-top-btn" data-pagina="livros">Livros</button>
                    <button class="menu-top-btn" data-pagina="alunos">Alunos</button>
                    <button class="trespontinhos" id="abrir-configuracoes">&#x22EE;</button>
                </div>
            </div>
        </div>
    </nav>
</header>