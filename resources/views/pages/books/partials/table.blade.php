<div class="table-wrapper" id="data-table-container">
    <table class="data-table" role="table" aria-label="Tabela de livros">
        <thead id="data-table-head"></thead>
        <tbody id="book-rows"></tbody>
    </table>
    <div id="loader" class="loader" style="text-align:center; padding:10px; display:none;">
        <span>Carregando mais livros...</span>
    </div>
</div>

<div id="button-prototypes" style="display:none;">
    <button data-icon="label" class="btn-dark btn-label" title="Gerar etiquetas">
        <x-icons.icon name="label" />
    </button>

    <button data-icon="plus" class="btn-dark btn-add" title="Adicionar novo livro">
        <x-icons.icon name="plus" />
    </button>
</div>
