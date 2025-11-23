<div class="table-wrapper" id="data-table-container">
    <table class="data-table" role="table" aria-label="Tabela de alunos">
        <thead id="data-table-head"></thead>
        <tbody id="student-rows"></tbody>
    </table>
    <div id="loader" class="loader" style="text-align:center; padding:10px; display:none;">
        <span>Carregando mais alunos...</span>
    </div>
</div>

<div id="icon-prototypes" style="display:none;">
    <span data-status="active">
        <x-icons.icon name="status-active" class="status-icon status-active" title="Ativo para empréstimos" />
    </span>

    <span data-status="blocked">
        <x-icons.icon name="status-blocked" class="status-icon status-blocked" title="Bloqueado para empréstimos" />
    </span>
</div>

<div id="button-prototypes" style="display:none;">
    <button data-icon="plus" class="btn-dark btn-add" title="Adicionar novo aluno">
        <x-icons.icon name="plus" />
    </button>
</div>
