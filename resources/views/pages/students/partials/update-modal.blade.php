<x-modals.modal id="studentUpdateModal" title="Atualizar Aluno">
    <x-slot name="content">
        <div class="form-row">
            <x-modals.input-field id="name" label="Nome do aluno" width="440px" :value="$student->name"
                title="Nome completo do aluno" />
            <x-modals.input-field id="cpf" label="CPF" width="160px" :value="$student->cpf" title="CPF do aluno" />

            <div class="form-group" style="display: flex; align-items: center;">
                <select id="course" name="course" class="form-input"
                    style="width: 315px; border-radius: 6px 0 0 6px; border-right: none; padding-right: 2px;"
                    title="Curso do aluno" data-value="{{ $student->course }}" required>

                    <option value="{{ $student->course }}" selected>{{ $student->course }}</option>
                </select>
                <label class="form-label" for="course"
                    style="display: flex; justify-content: space-between; align-items: center; padding-left: 8px;">
                    <span>Curso</span>
                </label>
                <div class="btn-input-side-wrapper">
                    <button id="open-create-school-classes-modal" class="btn-dark btn-input-side" style="border-radius: 0;"
                        title="Clique para adicionar turma" aria-label="Adicionar turma">
                        <x-icons.icon name="plus" class="" />
                    </button>
                </div>
            </div>

            <x-modals.select-field id="period" label="Período" width="100px" data-value="{{ $student->period }}"
                :value="$student->period" title="Período do curso" />
            <x-modals.select-field id="term" label="Regime" width="140px" data-value="{{ $student->term }}"
                :value="$student->term" title="Regime do curso" />
            <x-modals.input-field id="phone" label="Telefone" width="140px" :value="$student->phone"
                title="Telefone do aluno" />
            <x-modals.input-field id="email" label="Email" width="460px" :value="$student->email"
                title="Email do aluno" />
        </div>
    </x-slot name="content">

    <x-slot name="footer">
        <button type="button" class="modal-button" id="btn-cancel-update"
            title="Cancelar atualização" aria-label="Cancelar atualização">Cancelar</button>
        <button type="button" class="modal-button" id="submit-update"
            title="Salvar alterações do aluno" aria-label="Salvar alterações do aluno">Salvar</button>
    </x-slot name="footer">
</x-modals.modal>
