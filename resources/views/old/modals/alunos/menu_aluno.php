<div id="modal" class="modal" >
    <div class="modal-header">
        <h2 class="modal-title">Menu de Aluno</h2>
        <button id="botao-fechar-modal" class="modal-close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="modal-body">
        <div class="rec-inputs">
            <div class="entryarea">
                <input type="text" id="nome-aluno" class="modal-input" style="width: 450px;" readonly>
                <label class="labelline" for="nome-aluno">Nome do aluno</label>
            </div>
            <div class="entryarea">
                <input type="text" id="status-aluno" class="modal-input" style="width: 140px;" readonly>
                <label class="labelline" for="status-aluno">Status</label>
            </div>
            <div class="entryarea">
                <input type="text" id="periodo" class="modal-input" style="width: 123px;" readonly>
                <label class="labelline" for="periodo">Período</label>
            </div>
            <div class="entryarea">
                <input type="text" id="curso" class="modal-input" style="width: 300px;" readonly>
                <label class="labelline" for="curso">Curso</label>
            </div>
            <div class="entryarea">
                <input type="text" id="cpf" class="modal-input" style="width: 140px;" required>
                <label class="labelline" for="cpf">CPF</label>
            </div>
            <div class="entryarea">
                <input type="text" id="telefone" class="modal-input" style="width: 150px;" required>
                <label class="labelline" for="telefone">Telefone</label>
            </div>
            <div class="entryarea">
                <input type="text" id="email" class="modal-input" style="width: 440px;" required>
                <label class="labelline" for="email">Email</label>
            </div>

        </div>

        <div class="modal-footer">
            <button id="botao-excluir" data-entidade="emprestimo" class="modal-button">Excluir</button>
            <button id="botao-editar" class="modal-button">Editar</button>
        </div>
    </div>
</div>
