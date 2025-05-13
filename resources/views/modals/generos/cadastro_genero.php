<!-- Modal de Cadastro de Gênero -->
<div id="modal" class="modal">
    <!-- Cabeçalho do Modal -->
    <div class="modal-header">
        <h2 class="modal-title">Cadastro de Gênero</h2>
        <button id="botao-fechar-modal" class="modal-close">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Corpo do Modal -->
    <div class="modal-body">
        <div class="entryarea">
            <input 
                type="text" 
                id="genero" 
                class="modal-input" 
                style="width: 250px; border-radius: 6px;" 
                required>
            <label class="labelline" for="genero">Nome do Gênero</label>

            <div class="input-icon"></div>
        </div>

        <div class="modal-footer">
            <button id="botao-cancelar" class="modal-button">Cancelar</button>
            <button id="botao-cadastrar-genero" class="modal-button">Cadastrar</button>
        </div>
    </div>
</div>
