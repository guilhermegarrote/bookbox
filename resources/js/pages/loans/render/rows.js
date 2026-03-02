/**
 * Renders HTML table rows based on a table definition and a list of items.
 *
 * @param {Object} definition - Table definition object (columns + actions).
 * @param {Array<Object>} items - List of row objects returned from backend.
 * @returns {string} HTML string containing rendered <tr> rows.
 */
export function renderTableRows(definition, items) {
    return items.map(item => {
        const isReturned = item.loan_returned_date !== null;

        const rowClass = isReturned ? 'loan-returned' : '';

        return `
            <tr data-id="${item.id}" class="${rowClass}">
                ${definition.columns.map(c => {
            let cellValue = item[c.key] ?? '';

            if (c.key === 'loan_due_date' && cellValue) {
                const [year, month, day] = cellValue.split('-');
                const date = new Date(year, month - 1, day);
                const formattedDate = new Intl.DateTimeFormat('pt-BR').format(date);
                cellValue = formattedDate;
            }

            return `<td>${cellValue}</td>`;
        }).join('')}
                ${'<td class="button-col"></td>'.repeat(definition.actions.length)}
            </tr>
        `;
    }).join('');
}
