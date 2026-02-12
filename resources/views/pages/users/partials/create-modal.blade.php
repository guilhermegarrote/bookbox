<style>
    .modal-window {
        width: 500px;
    }
</style>

<x-modals.modal id="userCreateModal" title="Cadastrar Usuário">
    <x-slot name="content">
        <div style="display: flex; flex-direction: column; gap: 8px; align-items: center; margin-top: 10px; margin-bottom: 20px;">
            <x-modals.input-field id="name" label="Nome completo" width="350px" required
                title="Informe o nome completo do usuário" />
            <x-modals.input-field type="email" id="email" label="Email" width="350px" required
                title="Informe o email do usuário" />
            <x-modals.input-field type="password" id="password" label="Senha" width="350px" required
                title="Informe a senha" />
            <x-modals.input-field type="password" id="password_confirmation" label="Confirmar senha" width="350px" required
                title="Informe a senha novamente" />
        </div>
    </x-slot name="content">

    <x-slot name="footer">
        <button type="button" class="modal-button" id="btn-close" title="Cancelar o cadastro do usuário">Cancelar</button>
        <button type="button" class="modal-button" id="submit-create" title="Cadastrar usuário">Cadastrar</button>
    </x-slot name="footer">
</x-modals.modal>
