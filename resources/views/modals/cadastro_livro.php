<div id="modal" class="modal">
    <div class="modal-header">
        <h2 class="modal-title">Cadastro de Livro</h2>
        <button onclick="fecharModal()" class="modal-close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="modal-body">
        <div class="rec-inputs">
            <div class="entryarea">
                <input type="text" id="codigoIBSN" class="modal-input" oninput="mascararISBN(this)" style="width: 170px;" required>
                <label class="labelline" for="codigoIBSN">Código IBSN</label>
            </div>
            <div class="entryarea">
                <input type="text" id="titulo" class="modal-input" style="width: 420px;" required>
                <label class="labelline" for="titulo">Título</label>
            </div>
            <div class="entryarea">
                <input type="text" id="editora" class="modal-input" style="width: 340px;" required>
                <label class="labelline" for="editora">Editora</label>
            </div>
            <div class="entryarea">
                <input type="text" id="autor" class="modal-input" style="width: 250px;" required>
                <label class="labelline" for="autor">Autor</label>
            </div>
            <div class="entryarea" style="width: 175px;">
                <input type="text" id="genero" class="modal-input" style="width: 150px;" required>
                <label class="labelline" for="genero">Gênero</label>

                <div style="position: absolute; width: 35px; height: 35px;  top: 60%; left: 84.8%; border: 1px solid #ccc; border-radius: 6px;  transform: translateY(-60%); background-color: blue;"></div>
            </div>
            <div class="entryarea" style="right:25%;">
                <input type="text" id="quantidadeExemplares" class="modal-input" oninput="permitirSomenteNumeros(this)" style="width: 230px;" required>
                <label class="labelline" for="quantidadeExemplares">Quantidade de exemplares</label>
            </div>
        </div>

        <div class="modal-footer">
            <button onclick="fecharModal()" class="modal-button">Cancelar</button>
            <button onclick="cadastrarLivro(event)" class="modal-button">Cadastrar livro</button>
        </div>
    </div>
</div>