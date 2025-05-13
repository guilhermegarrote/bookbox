<div id="modal" class="modal">
    <div class="modal-header">
        <h2 class="modal-title">Cadastro de Aluno</h2>
        <button id="botao-fechar-modal" class="modal-close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="modal-body">
        <div class="rec-inputs">
            <div class="entryarea">
                <input type="text" id="nome" class="modal-input" style="width: 460px;" required>
                <label class="labelline" for="nome">Nome</label>
            </div>
            <div class="entryarea">
                <input type="text" id="periodo" class="modal-input" style="width: 140px;" required>
                <label class="labelline" for="periodo">Período</label>
            </div>
            <div class="entryarea">
                <input type="text" id="curso" class="modal-input" style="width: 300px;" required>
                <label class="labelline" for="curso">Curso</label>
            </div>
            <div class="entryarea">
                <input type="text" id="telefone" class="modal-input" style="width: 140px;" required>
                <label class="labelline" for="telefone">Telefone</label>
            </div>
            <div class="entryarea">
                <input type="text" id="cpf" class="modal-input" style="width: 140px;" required>
                <label class="labelline" for="cpf">CPF</label>
            </div>
            <div class="entryarea">
                <input type="text" id="email" class="modal-input" style="width: 460px;" required>
                <label class="labelline" for="email">Email</label>
            </div>
        </div>

        <div class="modal-footer">
            <button id="botao-fechar-modal" class="modal-button">Cancelar</button>
            <button id="botao-cadastrar" data-entidade="aluno" class="modal-button">Cadastrar</button>
        </div>
    </div>
</div>