<div id="modal" class="modal">
    <div class="modal-header">
        <h2 class="modal-title">Menu de Empréstimo</h2>
        <button id="botao-fechar-modal" class="modal-close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="modal-body">
        <div class="rec-inputs">
            <div class="entryarea">
                <input type="text" id="nome-aluno" class="modal-input" style="width: 620px;" readonly>
                <label class="labelline" for="nome-aluno">Nome do aluno</label>
            </div>
            <div class="entryarea">
                <input type="text" id="periodo" class="modal-input" style="width: 123px;" readonly>
                <label class="labelline" for="periodo">Período</label>
            </div>
            <div class="entryarea" style="right:25%;">
                <input type="text" id="curso" class="modal-input" style="width: 300px;" readonly>
                <label class="labelline" for="curso">Curso</label>
            </div>
             <div class="entryarea">
                <input type="text" id="nome-livro" class="modal-input" style="width: 620px;" readonly>
                <label class="labelline" for="nome-livro">Nome do livro</label>
            </div>
             <div class="entryarea">
                <input type="text" id="isbn" class="modal-input" style="width: 170px;" required>
                <label class="labelline" for="isbn">Código ISBN</label>
            </div>
            <div class="entryarea" style="right:23%; margin-right:20%;">
                <input type="text" id="exemplar" class="modal-input" style="width: 140px;" required>
                <label class="labelline" for="exemplar">Exemplar</label>
            </div>
            <div class="entryarea" >
                <input type="text" id="data-do-emprestimo" class="modal-input" style="width: 170px; " readonly>
                <label class="labelline" for="data-do-emprestimo">Data do empréstimo</label>
            </div>
            <div class="entryarea" style="right:2%;">
                <input type="text" id="data-devolucao" class="modal-input" style="width: 170px;" readonly>
                <label class="labelline" for="data-devolucao">Data da devolução</label>
            </div>
            <div class="entryarea" style="right:5%;">
                <input type="text" id="dias-em-atraso" class="modal-input" style="width: 170px;" readonly>
                <label class="labelline" for="dias-em-atraso">Dias em atraso</label>
            </div>
        </div>

        <div class="modal-footer">
            <button id="botao-prolongar-devolucao" class="modal-button">Prolongar devolução</button>
             <button id="botao-finalizar" data-entidade="emprestimo" class="modal-button">Finalizar</button>
        </div>
    </div>
</div>