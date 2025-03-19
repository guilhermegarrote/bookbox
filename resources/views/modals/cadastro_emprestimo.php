<div id="modal" class="modal">
    <div class="modal-header">
        <h2 class="modal-title">Cadastro de Empréstimo</h2>
        <button onclick="fecharModal()" class="modal-close">X</button>
    </div>
    <hr>

    <div class="modal-body">
        <label for="nomeEstudante" class="modal-label">Nome do Estudante:</label>
        <input type="text" id="nomeEstudante" class="modal-input" readonly><br><br>

        <label for="cpf" class="modal-label">CPF:</label>
        <input type="text" id="cpf" class="modal-input"><br><br>

        <label for="serie" class="modal-label">Série:</label>
        <input type="text" id="serie" class="modal-input" readonly><br><br>

        <label for="curso" class="modal-label">Curso:</label>
        <input type="text" id="curso" class="modal-input" readonly><br><br>

        <label for="codigoIBSN" class="modal-label">Código IBSN:</label>
        <input type="text" id="codigoIBSN" class="modal-input"><br><br>

        <label for="nomeLivro" class="modal-label">Nome do Livro:</label>
        <input type="text" id="nomeLivro" class="modal-input" readonly><br><br>

        <label for="exemplar" class="modal-label">Exemplar:</label>
        <input type="text" id="exemplar" class="modal-input" readonly><br><br>

        <label for="dataDevolucao" class="modal-label">Data da Devolução:</label>
        <input type="text" id="dataDevolucao" class="modal-input" readonly><br><br>
    </div>

    <div class="modal-footer">
        <button onclick="fecharModal()" class="modal-button">Cancelar</button>
        <button onclick="salvarDados()" class="modal-button">Realizar empréstimo</button>
    </div>
</div>