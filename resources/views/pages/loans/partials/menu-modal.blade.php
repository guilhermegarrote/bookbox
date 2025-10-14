<x-modals.modal id="loanMenuModal"
    title="Menu do Empréstimo {{ $loan->loan_returned_date ? '(Finalizado)' : '' }}">

    <x-slot name="content">
        <div class="form-row">
            <x-modals.input-field id="name" label="Nome do estudante" width="450px" :value="$loan->name" readonly
                title="Nome do estudante" />
            <x-modals.input-field id="cpf" label="CPF" width="150px" :value="$loan->cpf" readonly
                title="CPF do aluno" />
            <x-modals.input-field id="formatted_class" label="Turma" width="650px" :value="$loan->formatted_class" readonly
                title="Turma do aluno" />
            <x-modals.input-field id="isbn" label="Código ISBN" width="160px" :value="$loan->isbn" readonly
                title="Código IBSN do livro" />
            <x-modals.input-field id="title" label="Título" width="440px" :value="$loan->title" readonly
                title="Título do livro" />
            <x-modals.input-field id="number" label="Exemplar" width="100px" :value="$loan->number" readonly
                title="Quantidade de exemplar do livro" />
            <x-modals.input-field id="author" label="Autor" width="370px" :value="$loan->author" readonly
                title="Autor do livro" />
            <x-modals.input-field id="genre_name" label="Gênero" width="120px" :value="$loan->genre_name" readonly
                title="Gênero do livro" />
            <x-modals.input-field id="loan_start_date" label="Data de empréstimo" width="190px" :value="$loan->loan_start_date" readonly
                title="Data do empréstimo do livro" />
            <x-modals.input-field id="loan_due_date" label="Data da devolução" width="190px" :value="$loan->loan_due_date" readonly
                title="Data da devolução do livro" />
        </div>
    </x-slot>

    <x-slot name="footer">
        <button type="button" class="modal-button" id="btn-close" title="Fechar o menu do empréstimoi">Fechar</button>

        @unless($loan->loan_returned_date)
            <button type="button" class="modal-button" id="submit-finalize" title="Finalizar este empréstimo">Finalizar</button>
            <button type="button" class="modal-button" id="open-extend-modal" title="Prorrogar devolução">Prorrogar devolução</button>
        @endunless
    </x-slot>
</x-modals.modal>
