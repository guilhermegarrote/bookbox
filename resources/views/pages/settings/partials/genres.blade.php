<h2 class="config-title">Gerenciar Gêneros</h2>

<div class="search-add">
    <input type="search" id="item-search" data-entity="genres" placeholder="Busque pelo nome ou pela cor" />
    <button type="button" id="open-create-modal">Adicionar</button>
</div>

<div class="settings-card-list">
    @include('pages.settings.partials.genres-list', ['genres' => $genres])
</div>
