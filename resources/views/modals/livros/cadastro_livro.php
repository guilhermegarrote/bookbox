<div id="modal" class="modal">
    <div class="modal-header">
        <h2 class="modal-title">Cadastro de Livro</h2>
        <button id="botao-fechar-modal" class="modal-close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="modal-body">
        <div class="rec-inputs">
            <div class="entryarea">
                <input type="text" id="isbn" class="modal-input" style="width: 170px;" required>
                <label class="labelline" for="isbn">ISBN</label>
            </div>
            <div class="entryarea">
                <input type="text" id="titulo" class="modal-input" style="width: 420px;" required>
                <label class="labelline" for="titulo">Título</label>
            </div>
            <div class="entryarea">
                <input type="text" id="editora" class="modal-input" style="width: 340px;" required>
                <label class="labelline" for="editora">Editora</label>
            </div>
            <div class="entryarea">
                <input type="text" id="autor" class="modal-input" style="width: 250px;" required>
                <label class="labelline" for="autor">Autor</label>
            </div>
            <div class="entryarea" style="width: 175px;">
                <input type="text" id="genero" class="modal-input" style="width: 150px; border-radius: 6px 0px 0px 6px;" required>
                <label class="labelline" for="genero">Gênero</label>
                <div style="position: absolute; width: 35px; height: 35px; top: 60%; left: 84.8%; border: 1px solid #ccc; border-radius: 0px 6px 6px 0px; transform: translateY(-60%); background-color: blue;"></div>
            </div>
            <div class="entryarea" style="right: 25%;">
                <input type="text" id="quantidade-exemplares" class="modal-input" style="width: 230px;" required>
                <label class="labelline" for="quantidade-exemplares">Quantidade de exemplares</label>
            </div>
        </div>

        <div class="modal-footer">
            <button id="botao-fechar-modal" class="modal-button">Cancelar</button>
            <button class="modal-button">Cadastrar</button>
        </div>
    </div>
</div>