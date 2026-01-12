<h2 class="config-title">Gerenciar Turmas</h2>

<div class="search-add">
    <input type="search" placeholder="Buscar..." />
    <button type="button">Adicionar</button>
</div>

<div class="settings-card-list">
    @foreach ($classes as $class)
        <div class="settings-card improved-class-card">
            <div>
                <strong>{{ $class->course }}</strong><br>
                <small>{{ $class->period . '° ' . ($class->term == 'Annual' ? 'ano' : 'semestre') }}</small>
            </div>

            <div class="settings-actions">
                <button data-icon="pencil" class="btn-list-config" title="Editar turma">
                    <x-icons.icon name="pencil" />
                </button>

                <button data-icon="trash" class="btn-list-config" title="Excluir turma">
                    <x-icons.icon name="trash" />
                </button>
            </div>
        </div>
    @endforeach
</div>
