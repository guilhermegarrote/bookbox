export function renderTableRows(definition, items) {
    return items.map(item => `
        <tr data-id="${item.id}">
            ${definition.columns.map(col => {
                if (col.key === "can_borrow") {
                    return `<td>${renderStatusIcon(item.can_borrow)}</td>`;
                }

                return `<td>${item[col.key] ?? ''}</td>`;
            }).join('')}

            ${'<td class="button-col"></td>'.repeat(definition.actions.length)}
        </tr>
    `).join('');
}

function renderStatusIcon(canBorrow) {
    const prototypes = document.getElementById("icon-prototypes");
    if (!prototypes) return "";

    const span = prototypes.querySelector(
        canBorrow ? '[data-status="active"]' : '[data-status="blocked"]'
    );

    return span ? span.innerHTML : "";
}
