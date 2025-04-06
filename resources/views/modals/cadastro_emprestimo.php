<div id="modal" class="modal">
    <div class="modal-header">
        <h2 class="modal-title">Cadastro de Empréstimo</h2>
        <button onclick="fecharModal()" class="modal-close">X</button>
    </div>
    <div class="modal-body">
        <div class="rec-inputs">
            <div class="entryarea">
                <input type="text" id="nomeEstudante" class="modal-input" style="width: 450px;" readonly required>
                <label class="labelline" for="nomeEstudante">Nome do Estudante</label>
            </div>
            <div class="entryarea">
                <input type="text" id="status" class="modal-input" style="width: 140px;" readonly required>
                <label class="labelline" for="status">Status do aluno</label>
            </div>

            <div class="entryarea" >
                <input type="text" id="cpf" class="modal-input" style="width:140px;"required>
                <label class="labelline" for="cpf">CPF</label>
            </div>
            <div class="entryarea">
                <input type="text" id="serie" class="modal-input" style="width:123px;" readonly required>
                <label class="labelline" for="serie">Período</label>
            </div>
            <div class="entryarea ">
                <input type="text" id="curso" class="modal-input"  style="width:300px;" readonly required>
                <label class="labelline" for="curso">Curso</label>
            </div>

            <div class="entryarea">
                <input type="text" id="codigoIBSN" class="modal-input" style="width:170px;" required>
                <label class="labelline" for="codigoIBSN">Código IBSN</label>
            </div>
            <div class="entryarea">
                <input type="text" id="nomeLivro" class="modal-input" style="width:420px;" readonly required>
                <label class="labelline" for="nomeLivro">Nome do Livro</label>
            </div>
            <div class="entryarea">
                <input type="text" id="statusl" class="modal-input" style="width:140px;" readonly required>
                <label class="labelline" for="statusl">Status do livro</label>
            </div>
            <div class="entryarea" style="right:8%;" >
                <input type="text" id="exemplar" class="modal-input" style="width:140px;" readonly required>
                <label class="labelline" for="exemplar">Exemplar</label>
            </div>
            <div class="entryarea" style="right:16%;">
                <input type="text" id="dataDevolucao" class="modal-input" style="width:170px;" readonly required>
                <label class="labelline" for="dataDevolucao">Data de Devolução</label>
            </div>
        </div>

        <div class="modal-footer">
            <button onclick="fecharModal()" class="modal-button">Cancelar</button>
            <button onclick="realizarEmprestimo(event)" class="modal-button">Realizar Empréstimo</button>
        </div>
    </div>
</div>