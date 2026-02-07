/**
 * FilterUI Init Logic
 * -------------------
 * Initializes fields, restores saved values and binds listeners.
 */

import { loadSavedFilters, saveFilters } from './filter-ui-storage';
import { uniqueBy } from './filter-ui-utils';
import { populateSelect } from './filter-ui-select';
import { getCurrentParamsFromFields } from './filter-ui-params';
import { applyFilterInternal } from './filter-ui-apply';

/**
 * Initializes FilterUI instance.
 *
 * @param {FilterUI} instance
 * @returns {void}
 */
export const initFilterUIInternal = instance => {
    const savedFilters = loadSavedFilters();

    instance.fields.forEach(field => {
        if (!field.element) field.element = document.getElementById(field.key);
        if (!field.element) return;

        if (field.element.tagName === 'SELECT') {
            field.optionsData = uniqueBy(instance.filterData, field.key).map(item => ({
                value: item[field.key],
                label: field.formatLabel
                    ? field.formatLabel(item[field.key])
                    : item[field.key]
            }));

            if (savedFilters[field.key] !== undefined) {
                field.element.value = savedFilters[field.key];
            }

            populateSelect(
                field.element,
                field.optionsData,
                'value',
                'label',
                field.placeholder
            );
        }

        if (field.element.tagName === 'INPUT') {
            if (savedFilters[field.key] !== undefined) {
                field.element.value = savedFilters[field.key];
            }
        }

        field.element.addEventListener('change', () => {
            applyFilterInternal(instance);
            saveFilters(getCurrentParamsFromFields(instance.fields));
        });

        if (field.element.tagName === 'INPUT') {
            field.element.addEventListener('keyup', e => {
                if (e.key === 'Enter') {
                    applyFilterInternal(instance);
                    saveFilters(getCurrentParamsFromFields(instance.fields));
                }
            });
        }
    });

    if (instance.fields.some(f => f.element)) {
        applyFilterInternal(instance);
    }
};
