<x-modals.modal id="studentUpdateModal" title="Atualizar Aluno">
    <x-slot:content>
        <x-modals.input-field id="name" label="Nome do aluno" width="440px" :value="$student->name"
            title="Informe o nome completo do aluno" />
        <x-modals.input-field id="cpf" label="CPF" width="160px" :value="$student->formatted_cpf"
            title="Informe o CPF do aluno" />
        <x-modals.select-field id="course" label="Curso" width="350px" data-value="{{ $student->course }}"
            :value="$student->course" title="Selecione o curso do aluno" />
        <x-modals.select-field id="period" label="Período" width="140px" data-value="{{ $student->period }}"
            :value="$student->period" title="Selecione o regime" />
        <x-modals.select-field id="term" label="Regime" width="100px" data-value="{{ $student->term }}"
            :value="$student->term" title="Selecione o período do aluno" />
        <x-modals.input-field id="phone" label="Telefone" width="140px" :value="$student->formatted_phone"
            title="Informe o telefone do aluno" />
        <x-modals.input-field id="email" label="Email" width="460px" :value="$student->email"
            title="Informe o email do aluno" />
    </x-slot:content>

    <x-slot:footer>
        <button type="button" class="modal-button" id="btn-cancel-update"
            title="Cancelar atualização">Cancelar</button>
        <button type="button" class="modal-button" id="submit-update"
            title="Salvar alterações do aluno">Salvar</button>
    </x-slot:footer>
</x-modals.modal>
