<x-modals.modal id="loanCreateModal" title="Cadastrar Empréstimo">
    <x-slot name="content">
        <div class="form-row">
            <x-modals.input-field id="name" label="Nome do aluno" width="450px" readonly
                title="Nome do aluno" />
            <x-modals.input-field id="can_borrow" label="Status" width="150px" readonly
                title="Indica se o aluno pode realizar empréstimos" />
            <x-modals.input-field id="cpf" label="CPF" width="150px" required title="CPF do aluno" />
            <x-modals.input-field id="formatted_class_name" label="Turma" width="450px" readonly
                title="Turma do aluno" />
                <x-modals.input-field id="isbn" label="Código ISBN" width="160px" required
                    title="Informe o código ISBN do livro" />
            <x-modals.input-field id="title" label="Título do livro" width="440px" readonly
                title="Título do livro" />
            <x-modals.select-field id="copy_number" label="Exemplar" width="150px" required
                title="Informe o número do exemplar" />
            <x-modals.input-field id="loan_due_date" label="Data de devolução" width="165px" readonly
                title="Data de devolução do empréstimo" />
        </div>
    </x-slot name="content">

    <x-slot name="footer">
        <button type="button" class="modal-button" id="btn-close"
            title="Cancelar o cadastro do empréstimo">Cancelar</button>
        <button type="button" class="modal-button" id="submit-create" title="Cadastrar novo empréstimo">Realizar
            empréstimo</button>
    </x-slot name="footer">
</x-modals.modal>
