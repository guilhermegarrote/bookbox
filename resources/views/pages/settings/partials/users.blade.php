<h2 class="config-title">Gerenciar Usuários</h2>

<div class="search-add">
    <input type="search" id="item-search" data-entity="users" placeholder="Busque pelo nome ou email" />
    <button type="button">Adicionar</button>
</div>

<div class="settings-card-list">
    @include('pages.settings.partials.users-list', ['users' => $users])
</div>
