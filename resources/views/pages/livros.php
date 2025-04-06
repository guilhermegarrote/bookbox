<div class="containerTI">
    <div class="contentTI">
        <div class="tableContainerTI">
            <table class="tableTI">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all" onclick="selecionarTodos()"></th>
                        <th>Disponibilidade</th>
                        <th>IBSN</th>
                        <th>Titulo</th>
                        <th>Autor</th>
                        <th>Editora</th>
                        <th>Gênero</th>
                    </tr>
                </thead>
                <tbody id="livros-body">
                </tbody>
            </table>
        </div>

        <div id="overlay" style="justify-content: center;">
            <div id="modal-container"></div>
        </div>

        <div class="buttonsTI">
            <button class="btnTI" onclick="abrirModal('cadastro_livro')">Cadastrar</button>
            <button class="btnTI">Editar</button>
            <button class="btnTI">Gerar Etiqueta</button>
        </div>
    </div>
</div>