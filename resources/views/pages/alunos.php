<div class="containerTI">
    <div class="contentTI">
        <div class="tableContainerTI">
            <table class="tableTI">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all" onclick="selecionarTodos()"></th>
                        <th>Nome</th>
                        <th>Curso</th>
                        <th>Período</th>
                        <th>Horário</th>
                    </tr>
                </thead>
            </table>
            <div style="overflow: auto; height: 65vh;">
                <table class="tableTI">
                    <tbody id="alunos-body">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="buttonsTI">
            <button class="btnTI" onclick="abrirModal('cadastro_aluno')">Cadastrar</button>
            <button class="btnTI">Editar</button>
            <button class="btnTI">Excluir</button>
        </div>

        <div id="overlay" style="justify-content: center;">
            <div id="modal-container"></div>
        </div>
    </div>
</div>