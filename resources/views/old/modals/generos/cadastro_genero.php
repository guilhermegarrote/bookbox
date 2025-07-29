<div id="modal-genero" class="modal">
    <div class="modal-header">
        <h2 class="modal-title">Cadastro de Gênero</h2>
        <button id="botao-fechar-modal" class="modal-close">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="modal-body">
        <div class="rec-inputs" style="display: flex; align-items: center; flex-wrap: wrap; gap: 10px; justify-content: center;">
            <div class="entryarea" style="width: auto; display: flex; align-items: center;">
                <label class="label-fixa" for="cor-genero">Cor</label>
                <input type="text" id="cor-genero" class="modal-input" style="width: 150px; border-radius: 6px 0 0 6px; height: 35px;" required>

                <input type="color" id="cor-genero-picker" name="cor" style="opacity: 0; position: absolute; top: 0; left: 0; width: 100%; height: 100%; cursor: pointer; z-index: 2;">
                <div id="cor-preview" style="width: 35px; height: 35px; background-color: red; border: 1px solid #ccc; border-radius: 0 6px 6px 0;"></div>
            </div>

            <div class="entryarea" style="position: relative;">
                <input type="text" id="nome-genero" class="modal-input" style="width: 240px;" required>
                <label class="labelline" for="nome-genero">Nome</label>
            </div>
        </div>

        <div class="modal-footer">
            <button id="botao-fechar-modal" class="modal-button">Cancelar</button>
            <button id="botao-cadastar" data-entidade="livro" class="modal-button">Cadastrar</button>
        </div>
    </div>
</div>