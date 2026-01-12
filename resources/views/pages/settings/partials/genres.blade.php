<h2 class="config-title">Gerenciar Gêneros</h2>

<div class="search-add">
    <input type="search" placeholder="Buscar..." />
    <button type="button">Adicionar</button>
</div>

<div class="settings-card-list">
    @foreach ($genres as $genre)
        <div class="settings-card">
            <div style="display: flex; align-items: center; gap: 16px;">
                <span style="
                    width: 20px;
                    height: 20px;
                    border-radius: 50%;
                    background-color: {{ '#' . $genre['color_hex'] }};
                    display: inline-block;
                "></span>

                <div>
                    <strong>{{ $genre['name'] }}</strong><br>
                    <small>{{  "#" . $genre['color_hex'] }}</small>
                </div>
            </div>

            <div class="settings-actions">
                <button data-icon="pencil" class="btn-list-config" title="Editar gênero">
                    <x-icons.icon name="pencil" />
                </button>

                <button data-icon="trash" class="btn-list-config" title="Excluir gênero">
                    <x-icons.icon name="trash" />
                </button>
            </div>
        </div>
    @endforeach
</div>
