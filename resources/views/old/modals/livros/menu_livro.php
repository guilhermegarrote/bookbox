<div id="modal" class="modal">
    <div class="modal-header">
        <h2 class="modal-title">Menu de Livro</h2>
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
                <input type="text" id="disponibilidade" class="modal-input" style="width: 150px;" required>
                <label class="labelline" for="disponibilidade">Disponibilidade</label>
            </div>
            <div class="entryarea" >
                <input type="text" id="editora" class="modal-input" style="width: 440px;" required>
                <label class="labelline" for="editora">Editora</label>
            </div>
            <div class="entryarea">
                <input type="text" id="autor" class="modal-input" style="width: 400px;" required>
                <label class="labelline" for="autor">Autor</label>
            </div>
            <div class="entryarea" style="width: 175px; right:10px;">
                <input type="text" id="genero" class="modal-input" style="width: 150px;" required>
                <label class="labelline" for="genero">Gênero</label>
                <div style="position: absolute; width: 35px; height: 35px; top: 60%; left: 80.8%; border: 1.3px solid black; border-radius: 6px 6px 6px 6px; transform: translateY(-60%); background-color: blue; z-index:11;"></div>
            </div>
            <div class="entryarea"  >
               <input type="text" id="exemplar" class="modal-input" style="width: 120px;" required>
                <label class="labelline" for="exemplar">Exemplar</label>
                <button id="increment" class="quantity-btn" type="button" style="position: absolute; width: 35px; height: 35px; top: 60%; left: 93.8%; border: 1px solid black; border-radius: 6px 6px 6px 6px; transform: translateY(-60%); background-color: cinza; z-index:11;">+</button>
            </div>
        </div>

        <div class="modal-footer">
            <button id="botao-gerar-etiqueta" class="modal-button">Gerar Etiqueta</button>
            <button id="botao-editar" data-entidade="livro" class="modal-button">Editar</button>
            <button id="botao-excluir" data-entidade="livro" class="modal-button">Excluir</button>

        </div>
    </div>
</div>