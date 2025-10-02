<x-modals.modal id="studentCreateModal" title="Cadastro de Aluno">
    <x-slot:content>
        <div class="form-row">
            <x-modals.input-field id="name" label="Nome" width="440px" required
                title="Informe o nome completo do aluno" />
            <x-modals.input-field id="cpf" label="CPF" width="160px" required
                title="Informe o CPF do aluno (somente números)" />
            <x-modals.select-field id="course" label="Curso" width="350px" required
                title="Selecione o curso do aluno" />
            <x-modals.select-field id="term" label="Regime" width="140px" required
                title="Selecione o regime (Anual ou Semestral)" />
            <x-modals.select-field id="period" label="Período" width="100px" required
                title="Selecione o período do aluno" />
            <x-modals.input-field id="phone" label="Telefone" width="140px" required
                title="Informe o telefone de contato do aluno" />
            <x-modals.input-field id="email" label="Email" width="460px" required
                title="Informe o email do aluno" />
        </div>
    </x-slot:content>

    <x-slot:footer>
        <button type="button" class="modal-button" id="btn-close"
            title="Cancelar o cadastro do aluno">Cancelar</button>
        <button type="button" class="modal-button" id="submit-create" title="Cadastrar novo aluno">Cadastrar</button>
    </x-slot:footer>
</x-modals.modal>
