/**
 * FilterUI
 * --------
 * Manages filter fields and dataset filtering.
 */

import { saveFilters, loadSavedFilters } from './filter-ui-storage.js';
import { getCurrentParamsFromFields } from './filter-ui-params.js';
import { applyFilterInternal } from './filter-ui-apply.js';
import { initFilterUIInternal } from './filter-ui-init.js';
import { clearAllFiltersInternal } from './filter-ui-clear.js';
import { uniqueBy } from './filter-ui-utils.js';
import { populateSelect } from './filter-ui-select.js';

export class FilterUI {
    constructor({ filterData = [], fields = [], onParamsChange = () => {} }) {
        this.filterData = Array.isArray(filterData) ? filterData : [];
        this.fields = fields.map(f => ({ ...f }));
        this.onParamsChange = onParamsChange;

        if (!window.App) window.App = {};
        if (!window.App.filterUIInstance) window.App.filterUIInstance = this;
    }

    init() {
        initFilterUIInternal(this);
    }

    saveFilterState() {
        saveFilters(this.getCurrentParams());
    }

    clearAllFilters() {
        clearAllFiltersInternal(this);
    }

    getCurrentParams() {
        return getCurrentParamsFromFields(this.fields);
    }

    applyFilter() {
        applyFilterInternal(this);
    }

    updateData(newData, applyFilter = false) {
        const isSame = JSON.stringify(newData) === JSON.stringify(this.filterData);

        this.filterData = Array.isArray(newData) ? newData : [];

        if (applyFilter && !isSame && this.fields.some(f => f.element)) {
            this.applyFilter();
        }
    }

    initSelectsWithCurrentData() {
        this.fields.forEach(field => {
            if (!field.element || field.element.tagName !== 'SELECT') return;

            const savedFilters = loadSavedFilters();
            const currentValue = savedFilters[field.key] || '';

            const optionsData = uniqueBy(this.filterData, field.key).map(item => ({
                value: item[field.key],
                label: field.formatLabel
                    ? field.formatLabel(item[field.key])
                    : item[field.key]
            }));

            populateSelect(
                field.element,
                optionsData,
                'value',
                'label',
                field.placeholder
            );

            if (currentValue && optionsData.some(opt => opt.value == currentValue)) {
                field.element.value = currentValue;
            }
        });
    }
}
