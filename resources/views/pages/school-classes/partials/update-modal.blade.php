<x-modals.modal id="schoolClassUpdateModal" title="Cadastrar Turma">
    <x-slot name="content">
        <div class="form-row">
            <x-modals.input-field id="course" label="Curso" width="420px" title="Informe o curso da turma" :value="$schoolClass->course"/>

            <div class="form-group" style="width: 160px;" title="Informe o regime da turma">
                <select id="term" name="term" class="form-input" aria-label="Período" :value="$schoolClass->term">
                    <option value="Semester" selected>Semestre</option>
                    <option value="Annual" selected>Anual</option>
                </select>
                <label class="form-label" for="term">Período</label>
            </div>

            <x-modals.input-field id="start_date" label="Data Início" width="180px"
                title="Informe a data de início da turma" :value="$schoolClass->start_date" />
            <x-modals.input-field id="end_date" label="Data Fim" width="180px"
                title="Informe a data de fim da turma" :value="$schoolClass->end_date"/>
        </div>
    </x-slot name="content">

    <x-slot name="footer">
        <button type="button" class="modal-button" id="btn-close"
            title="Cancelar a atualização da turma">Cancelar</button>
        <button type="button" class="modal-button" id="submit-update" title="Atualizar turma">Atualizar</button>
    </x-slot name="footer">
</x-modals.modal>
