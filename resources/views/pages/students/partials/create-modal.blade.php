<x-modals.modal id="studentCreateModal" title="Cadastrar Aluno" :data-select="json_encode($selectData)">
    <x-slot name="content">
        <div class="form-row">
            <x-modals.input-field id="name" label="Nome" width="440px" required
                title="Informe o nome completo do aluno" />
            <x-modals.input-field id="cpf" label="CPF" width="160px" required title="Informe o CPF do aluno" />

            <div class="form-group" style="display: flex; align-items: center;">
                <select id="course" name="course" class="form-input"
                    style="width: 315px; border-radius: 6px 0 0 6px; border-right: none; padding-right: 2px;"
                    title="Selecione o curso do aluno" required>
                </select>
                <label class="form-label" for="course"
                    style="display: flex; justify-content: space-between; align-items: center; padding-left: 8px;">
                    <span>Curso</span>
                </label>
                <div class="btn-input-side-wrapper">
                    <button id="open-create-school-class-modal" class="btn-dark btn-input-side"
                        style="border-radius: 0;" title="Clique para adicionar turma" aria-label="Adicionar turma">
                        <x-icons.icon name="plus" class="" />
                    </button>
                </div>
            </div>

            <x-modals.select-field id="term" label="Regime" width="140px" required
                title="Selecione o regime do curso" />
            <x-modals.select-field id="period" label="Período" width="100px" required
                title="Selecione o período do curso" />
            <x-modals.input-field id="phone" label="Telefone" width="140px" required
                title="Informe o telefone de contato do aluno" />
            <x-modals.input-field type="email" id="email" label="Email" width="460px" required
                title="Informe o email do aluno" />
        </div>
    </x-slot name="content">

    <x-slot name="footer">
        <button type="button" class="modal-button" id="btn-close" title="Cancelar o cadastro do aluno"
            aria-label="Cancelar cadastro do aluno">Cancelar</button>
        <button type="button" class="modal-button" id="submit-create" title="Cadastrar novo aluno"
            aria-label="Cadastrar novo aluno">Cadastrar</button>
    </x-slot name="footer">
</x-modals.modal>
