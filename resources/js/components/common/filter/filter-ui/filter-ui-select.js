/**
 * FilterUI Select Helper
 * ----------------------
 * Handles populating a SELECT element with sorted unique options.
 */

/**
 * Populates a SELECT element with options.
 *
 * @param {HTMLSelectElement} select
 * @param {Array<Object>} items
 * @param {string} valueKey
 * @param {string} textKey
 * @param {string} [placeholderText='Selecione...']
 * @returns {void}
 */
export const populateSelect = (
    select,
    items = [],
    valueKey,
    textKey,
    placeholderText = 'Selecione...'
) => {
    if (!select) return;

    const currentValue = select.value || '';
    select.innerHTML = '';

    const fragment = document.createDocumentFragment();
    fragment.appendChild(new Option(placeholderText, ''));

    const uniqueItems = Array.from(new Set(items.map(item => item[valueKey])))
        .map(value => {
            const item = items.find(i => i[valueKey] === value);
            return {
                value: item[valueKey],
                label: item[textKey] ?? item[valueKey]
            };
        })
        .sort((a, b) =>
            String(a.label).localeCompare('pt-BR', { numeric: true })
        );

    uniqueItems.forEach(item => {
        const option = new Option(item.label, item.value);
        if (item.value === currentValue) option.selected = true;
        fragment.appendChild(option);
    });

    select.disabled = uniqueItems.length === 0;
    select.appendChild(fragment);
};
