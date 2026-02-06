/**
 * Renders table rows for a given dataset based on a column definition.
 *
 * @param {Object} definition - Table definition containing `columns` and `actions`
 * @param {Array<Object>} items - Array of data objects to render as rows
 * @returns {string} - HTML string of table rows
 */
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

/**
 * Returns the HTML for a status icon based on the canBorrow flag.
 *
 * @param {boolean|number} canBorrow - Flag indicating borrow status
 * @returns {string} - HTML of the status icon or empty string if not found
 */
function renderStatusIcon(canBorrow) {
    const prototypes = document.getElementById("icon-prototypes");
    if (!prototypes) return "";

    const span = prototypes.querySelector(
        canBorrow ? '[data-status="active"]' : '[data-status="blocked"]'
    );

    return span ? span.innerHTML : "";
}
