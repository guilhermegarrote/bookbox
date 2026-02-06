import { getButtonHTML } from "@js/components/utils/table-builder";

/**
 * Renders the table header HTML.
 *
 * @param {Object} definition
 * @param {Array} definition.columns
 * @param {Array} definition.actions
 * @returns {string}
 */
export function renderTableHead(definition) {
    return `
        <tr>
            ${definition.columns.map(col => `
                <th
                    ${col.sortable
                        ? `class="sort-link" data-sort="${col.key}" title="Clique para ordenar a coluna ${col.label}"`
                        : ""}
                >
                    ${col.label}
                </th>
            `).join('')}

            ${definition.actions.map(action => `
                <th class="button-col" style="width:${action.width}; text-align:right;">
                    ${getButtonHTML(action.icon)}
                </th>
            `).join('')}
        </tr>
    `;
}
