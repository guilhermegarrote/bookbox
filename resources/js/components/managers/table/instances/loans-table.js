import { sendRequest } from '@js/api/loans/fetch-list';
import TableManager from '@js/components/managers/table/table-manager';

import definition from '../definition/loans';

import { renderTableHead } from '../render/head';
import { renderTableRows } from '@js/pages/loans/render/rows';

document.addEventListener("DOMContentLoaded", () => {
    const head = document.querySelector("#data-table-head");
    if (head) {
        head.innerHTML = renderTableHead(definition);
    }
});

const loansTable = new TableManager({
    sendRequest,
    tableContainerId: 'data-table-container',
    errorMessage: definition.errorMessage ?? 'Erro ao carregar dados.',
    notFoundMessage: definition.notFoundMessage ?? 'Dados não encontrados.',
    renderRows: (items) => renderTableRows(definition, items),
});

export default loansTable;
