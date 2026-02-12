<style>
    .modal-window {
        width: 500px;
    }
</style>

<x-modals.modal id="userDeleteModal" title="Excluir Usuário">
    <x-slot name="content">
        <div
            style="display: flex; flex-direction: column; gap: 8px; align-items: center; margin-top: 10px; margin-bottom: 20px;">
            <x-modals.input-field type="password" id="password" label="Senha" width="350px" required
                title="Informe a senha" />
        </div>
    </x-slot name="content">

    <x-slot name="footer">
        <button type="button" class="modal-button" id="btn-close"
            title="Cancelar a exclusão do usuário">Cancelar</button>
        <button type="button" class="modal-button" id="submit-delete" title="Excluir usuário">Excluir</button>
    </x-slot name="footer">
</x-modals.modal>
