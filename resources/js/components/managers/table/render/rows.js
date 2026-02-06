/**
 * Renders table rows HTML based on a table definition and item list.
 *
 * @param {Object} definition
 * @param {Array} definition.columns
 * @param {Array} definition.actions
 * @param {Array<Object>} items
 * @returns {string}
 */
export function renderTableRows(definition, items) {
    return items.map(item => `
        <tr data-id="${item.id}">
            ${definition.columns.map(c => `<td>${item[c.key] ?? ''}</td>`).join('')}
            ${'<td class="button-col"></td>'.repeat(definition.actions.length)}
        </tr>
    `).join('');
}
