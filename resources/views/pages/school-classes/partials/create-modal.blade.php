<x-modals.modal id="schoolClassCreateModal" title="Cadastrar Turma">
    <x-slot name="content">
        <div class="form-row">
            <x-modals.input-field id="course" label="Curso" width="420px" required title="Informe o curso da turma" />

            <div class="form-group" style="width: 160px;" title="Informe o regime da turma">
                <select id="term" name="term" class="form-input" required aria-label="Período">
                    <option value="Semester" selected>Semestre</option>
                    <option value="Annual" selected>Anual</option>
                </select>
                <label class="form-label" for="term">Período</label>
            </div>

            <x-modals.input-field id="start_date" label="Data Início" width="180px" required
                title="Informe a data de início da turma" />
            <x-modals.input-field id="end_date" label="Data Fim" width="180px" required
                title="Informe a data de fim da turma" />
        </div>
    </x-slot name="content">

    <x-slot name="footer">
        <button type="button" class="modal-button" id="btn-close"
            title="Cancelar o cadastro de turmas">Cancelar</button>
        <button type="button" class="modal-button" id="submit-create" title="Cadastrar nova turma">Cadastrar</button>
    </x-slot name="footer">
</x-modals.modal>
