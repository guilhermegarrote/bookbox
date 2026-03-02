<x-modals.modal id="loanCreateModal" title="Cadastrar Empréstimo">
    <x-slot name="content">
        <div class="form-row">
            <x-modals.input-field id="name" label="Nome do aluno" width="450px" readonly title="Nome do aluno" />
            <x-modals.input-field id="can_borrow" label="Status" width="150px" readonly
                title="Indica se o aluno pode realizar empréstimos" />

            <div class="form-group" style="display: flex; align-items: center;">
                <input type="text" id="cpf" name="cpf" class="form-input" autocomplete="off"
                    aria-label="CPF" title="Informe o CPF do aluno"
                    style="width: 125px; border-radius: 6px 0 0 6px; border-right: none; padding-right: 2px;"
                    required />
                <label class="form-label" for="cpf"
                    style="display: flex; justify-content: space-between; align-items: center; padding-left: 8px;">CPF</label>
                <div class="btn-input-side-wrapper">
                    <button id="open-create-student-modal" class="btn-dark btn-input-side" style="border-radius: 0;"
                        title="Clique para adicionar aluno" aria-label="Adicionar aluno">
                        <x-icons.icon name="plus" class="" />
                    </button>
                </div>
            </div>

            <x-modals.input-field id="formatted_class_name" label="Turma" width="440px" readonly
                title="Turma do aluno" />

            <div class="form-group" style="display: flex; align-items: center;">
                <input type="text" id="isbn" name="isbn" class="form-input" autocomplete="off"
                    aria-label="ISBN"
                    style="width: 150px; border-radius: 6px 0 0 6px; border-right: none; padding-right: 2px;"
                    title="Informe o código ISBN do livro" required />
                <label class="form-label" for="isbn"
                    style="display: flex; justify-content: space-between; align-items: center; padding-left: 8px;">ISBN</label>
                <div class="btn-input-side-wrapper">
                    <button id="open-create-book-modal" class="btn-dark btn-input-side" style="border-radius: 0;"
                        title="Clique para adicionar livro" aria-label="Adicionar livro">
                        <x-icons.icon name="plus" class="" />
                    </button>
                </div>
            </div>

            <x-modals.input-field id="title" label="Título do livro" width="415px" readonly title="Título do livro"
                aria-label="Título do livro" />
            <x-modals.select-field id="copy_number" label="Exemplar" width="150px" readonly
                title="Informe o número do exemplar" aria-label="Número do exemplar" />
            <x-modals.input-field id="loan_due_date" label="Data de devolução" width="165px" readonly
                title="Data de devolução do empréstimo" aria-label="Data de devolução" value="{{ $loan_due_date }}"/>
        </div>
    </x-slot name="content">

    <x-slot name="footer">
        <button type="button" class="modal-button" id="btn-close" title="Cancelar o cadastro do empréstimo"
            aria-label="Cancelar cadastro do empréstimo">Cancelar</button>
        <button type="button" class="modal-button" id="submit-create" title="Cadastrar novo empréstimo"
            aria-label="Realizar empréstimo">Realizar
            empréstimo</button>
    </x-slot name="footer">
</x-modals.modal>
