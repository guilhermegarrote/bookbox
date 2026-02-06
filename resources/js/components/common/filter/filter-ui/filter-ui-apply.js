/**
 * FilterUI Apply Logic
 * --------------------
 * Applies filtering and updates select options dynamically.
 */

import { uniqueBy } from './filter-ui-utils.js';
import { populateSelect } from './filter-ui-select.js';
import { getCurrentParamsFromFields } from './filter-ui-params.js';

/**
 * Applies filtering to dataset and updates selects.
 *
 * @param {FilterUI} instance
 * @returns {void}
 */
export const applyFilterInternal = instance => {
    if (!Array.isArray(instance.filterData)) instance.filterData = [];

    const params = getCurrentParamsFromFields(instance.fields);

    instance.fields.forEach(f => {
        if (!f.element || f.element.tagName !== 'SELECT') return;

        let filteredOptions = [...instance.filterData];

        instance.fields.forEach(other => {
            if (!other.element || other === f) return;
            const val = other.element.value?.trim();

            if (val) {
                filteredOptions = filteredOptions.filter(
                    d => String(d[other.key]) === val
                );
            }
        });

        const currentVal = f.element.value;
        if (
            currentVal &&
            !filteredOptions.some(d => String(d[f.key]) === currentVal)
        ) {
            const original = instance.filterData.find(
                d => String(d[f.key]) === currentVal
            );
            if (original) filteredOptions.push(original);
        }

        const uniqueItems = uniqueBy(filteredOptions, f.key).map(item => ({
            value: item[f.key],
            label: f.formatLabel ? f.formatLabel(item[f.key]) : item[f.key]
        }));

        populateSelect(f.element, uniqueItems, 'value', 'label', f.placeholder);

        if (currentVal) f.element.value = currentVal;
    });

    instance.onParamsChange?.(params);

    document.dispatchEvent(new CustomEvent('filtersUpdated', { detail: params }));
};
