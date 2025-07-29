<x-modals.modal id="studentMenuModal" title="Menu de Aluno" close-id="close-menu-modal">
    <x-slot:content>
        <x-modals.input-field id="name" label="Nome do aluno" width="450px" :value="$student->name" readonly />
        <x-modals.input-field id="can_borrow" label="Status" width="150px" :value="$student->can_borrow ? 'Autorizado' : 'Bloqueado'" readonly />
        <x-modals.input-field id="cpf" label="CPF" width="150px" :value="$student->formatted_cpf" readonly />
        <x-modals.input-field id="email" label="Email" width="450px" :value="$student->email" readonly />
        <x-modals.input-field id="phone" label="Telefone" width="150px" :value="$student->formatted_phone" readonly />
        <x-modals.input-field id="formatted_class_name" label="Turma" width="450px" :value="$student->formatted_class_name" readonly />
    </x-slot:content>

    <x-slot:footer>
        <button type="button" class="modal-button" id="btn-close">Fechar</button>
        <button type="button" class="modal-button" id="submit-delete" data-entidade="emprestimo">Excluir</button>
        <button type="button" class="modal-button" id="btn-edit">Editar</button>
    </x-slot:footer>
</x-modals.modal>
