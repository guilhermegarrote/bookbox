import { sendRequest } from '@js/api/students/fetch-list';
import TableManager from '@js/components/managers/table/table-manager';

import definition from '../definition/students.js';

import { renderTableHead } from '../render/head.js';
import { renderTableRows } from '@js/pages/students/render/rows.js';

document.addEventListener("DOMContentLoaded", () => {
    const head = document.querySelector("#data-table-head");
    if (head) {
        head.innerHTML = renderTableHead(definition);
    }
});

const studentsTable = new TableManager({
    sendRequest,
    tableContainerId: 'data-table-container',
    errorMessage: definition.errorMessage ?? 'Erro ao carregar dados.',
    notFoundMessage: definition.notFoundMessage ?? 'Dados não encontrados.',
    renderRows: (items) => renderTableRows(definition, items),
});

export default studentsTable;
