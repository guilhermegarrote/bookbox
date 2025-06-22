<div class="containerTI">
    <div class="contentTI">
        <div class="tableContainerTI">
            <table class="tableTI">
                <thead>
                    <tr>
                        <th style="width: 5%"><input type="checkbox" id="select-all" onclick="selecionarTodos()"></th>
                        <th style="width: 18%;">Aluno</th>
                        <th style="width: 10%;">Sala</th>
                        <th style="width: 25%;">Livro</th>
                        <th style="width: 10%;">Exemplar</th>
                        <th style="width: 15%;">Empréstimo</th>
                        <th style="width: 15%;">Devolução</th>
                        <th style="width: 7%;">Atraso</th>
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
            <button class="btnTI" id="botao-abrir-modal" data-modal="menu_emprestimo" href="resources/views/modals/emprestimos/menu_emprestimo.php">Cadastrar</button>
            <button class="btnTI">Finalizar</button>
        </div>

        <div id="overlay">
            <div id="modal-container">
            </div>
        </div>
    </div>
</div>