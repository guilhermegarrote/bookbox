<x-modals.modal id="loanMenuModal" title="Menu de Empréstimo">
    <x-slot:content>
        <div class="form-row">
            <x-modals.input-field id="name" label="Nome do estudante" width="570px" :value="$loan->name" readonly
                title="Nome do estudante" />
            <x-modals.input-field id="period" label="Periodo" width="250px" :value="$loan->formatted_period" readonly
                title="Período que o estudante estuda" />
            <x-modals.input-field id="formatted_class_name-view" label="Curso" width="280px" :value="$loan->formatted_class_name" readonly
                title="Nome do curso do estudante" />
            <x-modals.input-field id="name" label="Nome do livro" width="570px" :value="$loan->name" readonly
                title="Nome do livro" />
            <x-modals.input-field id="formatted_class_name-view" label="Codigo IBSN" width="290px" :value="$loan->formatted_class_name" readonly
                title="Código IBSN do livro" />
            <x-modals.input-field id="formatted_class_name-view" label="Exemplar" width="130px" :value="$loan->formatted_class_name" readonly
                title="Quntidade de exemplar do livro" />
            <x-modals.input-field id="formatted_class_name-view" label="Data de empréstimo" width="190px" :value="$loan->formatted_class_name" readonly
                title="Data do empréstimo do livro" />
            <x-modals.input-field id="formatted_class_name-view" label="Data da devolução" width="190px" :value="$loan->formatted_class_name" readonly
                title="Data da devolução do livro" />
            <x-modals.input-field id="formatted_class_date-view" label="Dias em atraso" width="190px" :value="$loan->formatted_class_name" readonly
                title="Dias em atraso do empréstimo" />
        </div>
    </x-slot:content>

    <x-slot:footer>
        <button type="button" class="modal-button" id="btn-close" title="Fechar o menu do aluno">Fechar</button>
        <button type="button" class="modal-button" id="submit-delete" title="Excluir este emprestimo">Finalizar </button>
        <button type="button" class="modal-button" id="open-edit-modal" title="Abrir modal de edição">Pronlogar devolução</button>
    </x-slot:footer>
</x-modals.modal>