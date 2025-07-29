<x-modals.modal id="studentCreateModal" title="Cadastro de Aluno" close-id="close-create-modal">
    <x-slot:content>
        <x-modals.input-field id="name" label="Nome" width="440px" required />
        <x-modals.input-field id="cpf" label="CPF" width="160px" required />
        <x-modals.select-field id="student-course" label="Curso" width="350px" required />
        <x-modals.select-field id="student-period" label="Período" width="100px" required />
        <x-modals.select-field id="student-term" label="Regime" width="140px" required />
        <x-modals.input-field id="phone" label="Telefone" width="140px" required />
        <x-modals.input-field id="email" label="Email" width="460px" required />
    </x-slot:content>

    <x-slot:footer>
        <button type="button" class="modal-button" id="btn-close">Cancelar</button>
        <button type="button" class="modal-button" id="submit-create">Cadastrar</button>
    </x-slot:footer>
</x-modals.modal>
