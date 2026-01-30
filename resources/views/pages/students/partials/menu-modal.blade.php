<x-modals.modal id="studentMenuModal" title="Menu do Aluno">
    <x-slot name="content">
        <div class="form-row">
            <x-modals.input-field id="name" label="Nome do aluno" width="450px" :value="$student->name" readonly
                title="Nome completo do aluno" />
            <x-modals.input-field id="can_borrow" label="Status" width="150px" :value="$student->can_borrow ? 'Autorizado' : 'Bloqueado'" readonly
                title="Indica se o aluno pode realizar empréstimos" />
            <x-modals.input-field id="cpf" label="CPF" width="150px" :value="$student->cpf" readonly
                title="CPF do aluno" />
            <x-modals.input-field id="formatted_class_name" label="Turma" width="450px" :value="$student->formatted_class_name" readonly
                title="Nome da turma do aluno" />
            <x-modals.input-field id="phone" label="Telefone" width="140px" :value="$student->phone" readonly
                title="Telefone de contato do aluno" />
            <x-modals.input-field id="email" label="Email" width="460px" :value="$student->email" readonly
                title="Email do aluno" />
        </div>
    </x-slot name="content">

    <x-slot name="footer">
        <button type="button" class="modal-button" id="btn-close" title="Fechar o menu do aluno" aria-label="Fechar o menu do aluno">Fechar</button>
        <button type="button" class="modal-button" id="submit-delete" title="Excluir este aluno" aria-label="Excluir este aluno">Excluir</button>
        <button type="button" class="modal-button" id="open-edit-modal" title="Abrir modal de edição" aria-label="Abrir modal de edição">Editar</button>
    </x-slot name="footer">
</x-modals.modal>
