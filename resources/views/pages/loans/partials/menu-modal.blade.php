<x-modals.modal id="loanMenuModal" title="Menu do Empréstimo {{ $loan->loan_returned_date ? '(Finalizado)' : '' }}">

    <x-slot name="content">
        <div class="form-row">
            <x-modals.input-field id="name" label="Nome do estudante" width="450px" :value="$loan->name" readonly
                title="Nome do estudante" />
            <x-modals.input-field id="cpf" label="CPF" width="150px" :value="$loan->cpf" readonly
                title="CPF do aluno" />
            <x-modals.input-field id="formatted_class_name" label="Turma" width="650px" :value="$loan->formatted_class_name" readonly
                title="Turma do aluno" />
            <x-modals.input-field id="isbn" label="Código ISBN" width="160px" :value="$loan->isbn" readonly
                title="Código IBSN do livro" />
            <x-modals.input-field id="title" label="Título" width="440px" :value="$loan->title" readonly
                title="Título do livro" />
            <x-modals.input-field id="number" label="Exemplar" width="120px" :value="$loan->number" readonly
                title="Quantidade de exemplar do livro" />
            <x-modals.input-field id="author" label="Autor" width="480px" :value="$loan->author" readonly
                title="Autor do livro" />
            <div class="form-group" style="width: 200px; display: flex; align-items: center;">
                <input type="text" id="genre_name" name="genre_name" class="form-input"
                    value="{{ $loan->genre_name }}" readonly aria-label="Gênero do livro"
                    style="width: 165px; border-radius: 6px 0 0 6px;" title="Gênero do livro">
                <label class="form-label" for="genre_name"
                    style="display: flex; justify-content: space-between; align-items: center; padding-left: 8px;">
                    <span>Gênero do livro</span>
                </label>
                <div id="genre-color-preview"
                    style="width: 35px; height: 35px; background-color: {{ '#' . ($loan->genre_color_hex ?? 'ccc') }}; border: 1px solid #000; border-left: none; border-radius: 0 6px 6px 0;"
                    title="Cor do gênero"></div>
            </div>
            <x-modals.input-field id="loan_start_date" label="Data de empréstimo" width="195px" :value="$loan_start_date"
                readonly title="Data do empréstimo do livro" />
            <x-modals.input-field id="loan_due_date" label="Data da devolução" width="195px" :value="$loan_due_date" readonly
                title="Data da devolução do livro" />
        </div>
    </x-slot>

    <x-slot name="footer">
        <button type="button" class="modal-button" id="btn-close" title="Fechar o menu do empréstimoi">Fechar</button>

        @unless ($loan->loan_returned_date)
            <button type="button" class="modal-button" id="submit-finalize"
                title="Finalizar este empréstimo">Finalizar</button>
            <button type="button" class="modal-button" id="open-extend-modal" title="Prorrogar devolução">Prorrogar
                devolução</button>
        @endunless
    </x-slot>
</x-modals.modal>
