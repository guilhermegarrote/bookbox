    @foreach ($users as $user)
        <div class="settings-card" data-id="{{ $user->id }}">
            <div>
                <strong>{{ $user->name }}</strong><br>
                <small>{{ $user->email }}</small>
            </div>

            @if ($user->email == auth()->user()->email)
                <div class="settings-actions">
                    <button data-icon="pencil" class="btn-list-config" data-action="edit" title="Editar usuário">
                        <x-icons.icon name="pencil" />
                    </button>

                    <button data-icon="trash" class="btn-list-config" data-action="delete" title="Excluir usuário">
                        <x-icons.icon name="trash" />
                    </button>
                </div>
            @endif
        </div>
    @endforeach
