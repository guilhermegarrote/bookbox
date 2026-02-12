<h2 class="config-title">Gerenciar Turmas</h2>

<div class="search-add">
    <input type="search" id="item-search" data-entity="school-classes" placeholder="Busque pelo curso" />
    <button type="button" id="open-create-modal">Adicionar</button>
</div>

<div class="settings-card-list">
    @include('pages.settings.partials.school-classes-list', ['schoolClasses' => $schoolClasses])
</div>
