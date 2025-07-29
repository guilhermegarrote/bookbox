<div id="modal" class="modal">
    <div class="modal-header">
        <h2 class="modal-title">Cadastro de Empréstimo</h2>
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
                <label class="labelline" for="status-aluno">Status do aluno</label>
            </div>
            <div class="entryarea">
                <input type="text" id="cpf" class="modal-input" style="width: 140px;" required>
                <label class="labelline" for="cpf">CPF</label>
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
                <input type="text" id="isbn" class="modal-input" style="width: 170px;" required>
                <label class="labelline" for="isbn">ISBN</label>
            </div>
            <div class="entryarea">
                <input type="text" id="titulo" class="modal-input" style="width: 420px;" readonly>
                <label class="labelline" for="titulo">Título</label>
            </div>
            <div class="entryarea">
                <input type="text" id="exemplar" class="modal-input" style="width: 140px;" required>
                <label class="labelline" for="exemplar">Exemplar</label>
            </div>
            <div class="entryarea" style="right:50px;">
                <input type="text" id="status-livro" class="modal-input" style="width: 140px; " readonly>
                <label class="labelline" for="status-livro">Status do livro</label>
            </div>
            <div class="entryarea" style="right:100px;">
                <input type="text" id="data-devolucao" class="modal-input" style="width: 170px;" readonly>
                <label class="labelline" for="data-devolucao">Data de devolução</label>
            </div>
        </div>
        <div class="modal-footer">
            <button id="botao-fechar-modal" class="modal-button">Cancelar</button>
             <button id="botao-cadastrar" data-entidade="emprestimo" class="modal-button">Cadastrar</button>
        </div>
    </div>
</div>