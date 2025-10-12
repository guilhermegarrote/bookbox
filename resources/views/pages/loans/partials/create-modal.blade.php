<x-modals.modal id="loanCreateModal" title="Cadastro de Empréstimo">
    <x-slot:content>
        <div class="form-row">
            <x-modals.input-field id="name" label="Nome do aluno" width="480px" required
                title="Informe o nome completo do aluno" />
            <x-modals.input-field id="status" label="Status" width="75px" required
                title="Informe o status do estudante" />   
            <x-modals.input-field id="cpf" label="CPF" width="160px" required
                title="Informe o CPF do aluno (somente números)" />
            <x-modals.select-field id="period" label="Período" width="100px" required
                title="Selecione o período do estudante" />
            <x-modals.select-field id="course" label="Curso" width="300px" required
                title="Selecione o curso do aluno" />
            <x-modals.input-field id="name" label="Nome do livro" width="400px" required
                title="Informe o nome completo do livro" />
            <x-modals.input-field id="IBSN" label="Código IBSN" width="150px" required
                title="Informe o código IBNS do livro" />
            <x-modals.input-field id="date" label="Data de devolução" width="165px"
                title="Informe a data de devolução do empréstimo" />
            <x-modals.input-field id="Exemplar" label="Exemplar" width="100px"
                title="Informe a quantidade de exemplares" />

        </div>
    </x-slot:content>

    <x-slot:footer>
        <button type="button" class="modal-button" id="btn-close"
            title="Cancelar o cadastro do aluno">Cancelar</button>
        <button type="button" class="modal-button" id="submit-create" title="Cadastrar novo Empréstimo">Realizar empréstimo</button>
    </x-slot:footer>
</x-modals.modal>