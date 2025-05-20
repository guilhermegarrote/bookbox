<div id="modal-turma" class="modal">
    <div class="modal-header">
        <h2 class="modal-title">Cadastro de turma</h2>
        <button id="botao-fechar-modal" class="modal-close">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="modal-body">
        <div class="rec-inputs" style="display: flex; align-items: center; flex-wrap: wrap; gap: 10px; justify-content: center;">
            <div class="entryarea" style="position: relative;">
                <input type="text" id="curso" class="modal-input" style="width: 350px;" required>
                <label class="labelline" for="curso">Curso</label>
            </div>

            <div class="entryarea" style="position: relative;">
                <input type="text" id="periodo" class="modal-input" style="width: 200px;" required>
                <label class="labelline" for="periodo">Período</label>
            </div>

            <div class="entryarea" style="position: relative;">
                <input type="text" id="regime" class="modal-input" style="width: 180px;" required>
                <label class="labelline" for="regime">Regime</label>
            </div>

           <div class="entryarea" style="position: relative;">
                <input type="date" id="data-inicio" class="modal-inputt" required>
                <label class="labelline label-fixa" for="data-inicio">Data Início</label>
            </div>

            <div style="display: flex; align-items: center; justify-content: center; height: 35px; margin-top: -10px;">
                <span style="font-size: 30px; line-height: 1;">→</span>
            </div>


            <div class="entryarea" style="position: relative;">
                <input type="date" id="data-fim" class="modal-input" required>
                <label class="labelline label-fixa" for="data-fim">Data Fim</label>
            </div>
        </div>

        <div class="modal-footer">
            <button id="botao-fechar-modal" class="modal-button">Cancelar</button>
            <button id="botao-cadastar" data-entidade="turma" class="modal-button">Cadastrar</button>
        </div>
    </div>
</div>
