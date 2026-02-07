import { sendRequest } from '@js/api/books/fetch-list';
import TableManager from '@js/components/managers/table/table-manager';

import definition from '../definition/books';

import { renderTableHead } from '../render/head';
import { renderTableRows } from '../render/rows';

document.addEventListener("DOMContentLoaded", () => {
    const head = document.querySelector("#data-table-head");
    if (head) {
        head.innerHTML = renderTableHead(definition);
    }
});

const booksTable = new TableManager({
    sendRequest,
    tableContainerId: 'data-table-container',
    errorMessage: definition.errorMessage ?? 'Erro ao carregar dados.',
    notFoundMessage: definition.notFoundMessage ?? 'Dados não encontrados.',
    renderRows: (items) => renderTableRows(definition, items),
});

export default booksTable;
