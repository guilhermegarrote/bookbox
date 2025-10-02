<x-modals.modal id="studentMenuModal" title="Menu de Aluno">
    <x-slot:content>
        <div class="form-row">
            <x-modals.input-field id="name" label="Nome do aluno" width="450px" :value="$student->name" readonly
                title="Nome completo do aluno" />
            <x-modals.input-field id="can_borrow-view" label="Status" width="150px" :value="$student->can_borrow ? 'Autorizado' : 'Bloqueado'" readonly
                title="Indica se o aluno pode realizar empréstimos" />
            <x-modals.input-field id="cpf" label="CPF" width="150px" :value="$student->formatted_cpf" readonly
                title="CPF do aluno" />
            <x-modals.input-field id="formatted_class_name-view" label="Turma" width="450px" :value="$student->formatted_class_name" readonly
                title="Nome da turma do aluno" />
            <x-modals.input-field id="phone" label="Telefone" width="140px" :value="$student->formatted_phone" readonly
                title="Telefone de contato do aluno" />
            <x-modals.input-field id="email" label="Email" width="460px" :value="$student->email" readonly
                title="Email do aluno" />
        </div>
    </x-slot:content>

    <x-slot:footer>
        <button type="button" class="modal-button" id="btn-close" title="Fechar o menu do aluno">Fechar</button>
        <button type="button" class="modal-button" id="submit-delete" title="Excluir este aluno">Excluir</button>
        <button type="button" class="modal-button" id="open-edit-modal" title="Abrir modal de edição">Editar</button>
    </x-slot:footer>
</x-modals.modal>
