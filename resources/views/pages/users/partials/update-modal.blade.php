<style>
    .modal-window {
        width: 500px;
    }
</style>

<x-modals.modal id="userUpdateModal" title="Atualizar Usuário">
    <x-slot name="content">
        <div style="display: flex; flex-direction: column; gap: 8px; align-items: center; margin-top: 10px; margin-bottom: 20px;">
            <x-modals.input-field id="name" label="Nome completo" width="350px" :value="$user->name"
                title="Informe o nome completo do usuário" />
            <x-modals.input-field type="email" id="email" label="Email" width="350px" :value="$user->email"
                title="Informe o email do usuário" />
            <x-modals.input-field type="password" id="password" label="Senha" width="350px" title="Informe a senha" />
            <x-modals.input-field type="password" id="password_confirmation" label="Confirmar senha" width="350px"  title="Informe a senha novamente" />
        </div>
    </x-slot name="content">

    <x-slot name="footer">
        <button type="button" class="modal-button" id="btn-close"
            title="Cancelar atualização">Cancelar</button>
        <button type="button" class="modal-button" id="submit-update"
            title="Salvar alterações do Usuário">Salvar</button>
    </x-slot name="footer">
</x-modals.modal>
