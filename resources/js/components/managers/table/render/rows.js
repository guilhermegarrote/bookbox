export function renderTableRows(definition, items) {
    return items.map(item => `
        <tr data-id="${item.id}">
            ${definition.columns.map(c => `<td>${item[c.key] ?? ''}</td>`).join('')}
            ${'<td class="button-col"></td>'.repeat(definition.actions.length)}
        </tr>
    `).join('');
}
