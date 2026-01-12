<h2 class="config-title">Gerenciar Turmas</h2>

<div class="search-add">
    <input type="search" id="item-search" data-entity="classes" placeholder="Busque pelo curso" />
    <button type="button">Adicionar</button>
</div>

<div class="settings-card-list">
    @include('pages.settings.partials.classes-list', ['classes' => $classes])
</div>
