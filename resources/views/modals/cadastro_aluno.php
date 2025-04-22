<div id="modal" class="modal">
    <div class="modal-header">
        <h2 class="modal-title">Cadastro de Aluno</h2>
        <button onclick="fecharModal()" class="modal-close">X</button>
    </div>
    <div class="modal-body">
        <div class="rec-inputs">
            <div class="entryarea">
                <input type="text" id="" class="modal-input" style="width: 460px;" required>
                <label class="labelline" for="nomeEstudante">Nome do Estudante</label>
            </div>
            <div class="entryarea">
                <input type="text" id="" class="modal-input" style="width:140px;" required>
                <label class="labelline" for="">Período</label>
            </div>

            <div class="entryarea" >
                <input type="text" id="" class="modal-input" style="width:300px;"required>
                <label class="labelline" for="">Curso</label>
            </div>
            <div class="entryarea">
                <input type="text" id="" class="modal-input" style="width:140px;" required>
                <label class="labelline" for="">Telefone</label>
            </div>
            <div class="entryarea ">
                <input type="text" id="" class="modal-input"  style="width:140px;" required>
                <label class="labelline" for="">Cpf</label>
            </div>

            <div class="entryarea">
                <input type="text" id="" class="modal-input" style="width:460px;" required>
                <label class="labelline" for="">Email</label>
            </div>
            </div>
        </div>

        <div class="modal-footer">
            <button onclick="fecharModal()" class="modal-button">Cancelar</button>
            <button onclick="cadastrarAluno(event)" class="modal-button">Cadastro de Aluno</button>
        </div>
    </div>
</div>