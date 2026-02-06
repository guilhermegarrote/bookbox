/**
 * FilterUI Clear Logic
 * --------------------
 * Clears all filters and resets selects.
 */

import { uniqueBy } from './filter-ui-utils.js';
import { populateSelect } from './filter-ui-select.js';
import { clearSavedFilters } from './filter-ui-storage.js';

/**
 * Clears all filter values.
 *
 * @param {FilterUI} instance
 * @returns {void}
 */
export const clearAllFiltersInternal = instance => {
    instance.fields.forEach(f => {
        if (!f.element) return;

        const el = f.element;

        if (el.tagName === 'SELECT') {
            el.value = '';
        } else if (el.tagName === 'INPUT') {
            if (el.type === 'checkbox' || el.type === 'radio') {
                el.checked = false;
            } else {
                el.value = '';
            }
        } else if (el.tagName === 'TEXTAREA') {
            el.value = '';
        }
    });

    clearSavedFilters();

    instance.fields.forEach(f => {
        if (!f.element || f.element.tagName !== 'SELECT') return;

        const allOptions = uniqueBy(instance.filterData, f.key)
            .map(item => ({
                value: item[f.key],
                label: f.formatLabel ? f.formatLabel(item[f.key]) : item[f.key]
            }))
            .sort((a, b) =>
                String(a.label).localeCompare('pt-BR', { numeric: true })
            );

        populateSelect(f.element, allOptions, 'value', 'label', f.placeholder);
        f.element.value = '';
    });

    const emptyParams = {};
    instance.onParamsChange?.(emptyParams);

    document.dispatchEvent(
        new CustomEvent('filtersUpdated', { detail: emptyParams })
    );
};
