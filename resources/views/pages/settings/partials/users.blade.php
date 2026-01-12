<h2 class="config-title">Gerenciar Usuários</h2>

<div class="settings-card-list">
    @foreach ($users as $user)
        <div class="settings-card">
            <div>
                <strong>{{ $user->name }}</strong><br>
                <small>{{ $user->email }}</small>
            </div>

            <div class="settings-actions">
                <button data-icon="pencil" class="btn-list-config" title="Editar usuário">
                    <x-icons.icon name="pencil" />
                </button>

                <button data-icon="trash" class="btn-list-config" title="Excluir usuário">
                    <x-icons.icon name="trash" />
                </button>
            </div>
        </div>
    @endforeach
</div>
