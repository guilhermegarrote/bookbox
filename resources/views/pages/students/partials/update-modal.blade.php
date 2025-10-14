<x-modals.modal id="studentUpdateModal" title="Atualizar Aluno">
    <x-slot name="content">
        <div class="form-row">
            <x-modals.input-field id="name" label="Nome do aluno" width="440px" :value="$student->name"
                title="Nome completo do aluno" />
            <x-modals.input-field id="cpf" label="CPF" width="160px" :value="$student->cpf" title="CPF do aluno" />
            <x-modals.select-field id="course" label="Curso" width="350px" data-value="{{ $student->course }}"
                :value="$student->course" title="Curso do aluno" />
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
            title="Cancelar atualização">Cancelar</button>
        <button type="button" class="modal-button" id="submit-update"
            title="Salvar alterações do aluno">Salvar</button>
    </x-slot name="footer">
</x-modals.modal>
