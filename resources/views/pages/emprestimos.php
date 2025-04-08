<div class="containerTI">
    <div class="contentTI">
        <div class="tableContainerTI">
            <table class="tableTI">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all" onclick="selecionarTodos()"></th>
                        <th>Aluno</th>
                        <th>Sala</th>
                        <th>Livro</th>
                        <th>Exemplar</th>
                        <th>Data de Empréstimo</th>
                        <th>Data de Devolução</th>
                        <th>Atraso</th>
                    </tr>
                </thead>
            </table>
            <div style="overflow: auto; height: 65vh;">
                <table class="tableTI">
                    <tbody id="emprestimos-body">
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="buttonsTI">
            <button class="btnTI" onclick="abrirModal('cadastro_emprestimo')">Cadastrar</button>
            <button class="btnTI">Finalizar</button>
        </div>
        
        <div id="overlay">
            <div id="modal-container">
            </div>
        </div>
    </div>
</div>