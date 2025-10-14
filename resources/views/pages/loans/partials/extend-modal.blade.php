<div id="loanExtendModal" class="modal-window" aria-hidden="true" style="width: 450px;">
    <div class="modal-header">
        <h2 class="modal-title">Prorrogar empréstimo</h2>
    </div>

    <div class="modal-content">
        <div class="form-row" style="flex-direction: column; align-items: center; text-align: center;">
            <h3 style="font-size: 1.2rem; font-weight: bold; margin-bottom: 15px;">
                Tem certeza que deseja prorrogar?
            </h3>

            <div style="display: flex; flex-direction: column; gap: 10px; width: 100%; max-width: 300px;">
                <x-modals.input-field
                    id="current_date"
                    label="Data de devolução atual"
                    width="100%"
                    :value="$currentDate ?? ''"
                    title="Data de devolução atual"
                    disabled="true" />

                <x-modals.input-field
                    id="extended_date"
                    label="Data de devolução prolongada"
                    width="100%"
                    :value="$extendedDate ?? ''"
                    title="Nova data de devolução"
                    disabled="true" />
            </div>
        </div>

        <div class="modal-footer">
            <button id="modal-decline" class="modal-button">Cancelar</button>
            <button id="modal-accept" class="modal-button">Sim</button>
        </div>
    </div>
</div>
