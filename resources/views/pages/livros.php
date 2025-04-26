<div class="containerTI">
    <div class="contentTI">
        <div class="tableContainerTI">
            <table class="tableTI">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all" onclick="selecionarTodos()"></th>
                        <th>Disponibilidade</th>
                        <th>ISBN</th>
                        <th>Titulo</th>
                        <th>Autor</th>
                        <th>Editora</th>
                        <th>Gênero</th>
                    </tr>
                </thead>
            </table>
            <div style="overflow: auto; height: 65vh;">
                <table class="tableTI">
                    <tbody id="livros-body">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="buttonsTI">
            <button class="btnTI" id="botao-abrir-modal" data-modal="cadastro_livro">Cadastrar</button>
            <button class="btnTI" id="botao-abrir-modal" data-modal="menu_livro">Editar</button>
            <button class="btnTI">Gerar Etiqueta</button>
        </div>

        <div id="overlay">
            <div id="modal-container">
            </div>
        </div>
    </div>
</div>