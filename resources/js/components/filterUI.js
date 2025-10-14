export class FilterUI {
    constructor({ filterData = [], fields = [], onParamsChange = () => { } }) {
        this.filterData = Array.isArray(filterData) ? filterData : [];
        this.fields = fields;
        this.onParamsChange = onParamsChange;

        window.filterUIInstance = this;
    }

    init() {
        this.fields.forEach(field => {
            if (!field.element) return;

            if (field.element.tagName === 'SELECT') {
                field.optionsData = this.uniqueBy(this.filterData, field.key)
                    .map(item => ({
                        value: item[field.key],
                        label: field.formatLabel ? field.formatLabel(item[field.key]) : item[field.key]
                    }));
            }

            field.element.addEventListener('change', () => this.applyFilter(field));

            if (field.element.tagName === 'INPUT') {
                field.element.addEventListener('keyup', e => {
                    if (e.key === 'Enter') this.applyFilter(field);
                });
            }
        });

        if (this.fields.some(f => f.element)) {
            this.applyFilter();
        }
    }

    clearAllFilters() {
        this.fields.forEach(f => {
            if (!f.element) return;
            const el = f.element;

            switch (el.tagName) {
                case 'SELECT':
                    if (f.optionsData) {
                        this.populateSelect(el, f.optionsData, 'value', 'label', f.placeholder);
                    } else {
                        el.innerHTML = '';
                        el.appendChild(new Option(f.placeholder || 'Selecione...', '', true, true));
                    }
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                    break;
                case 'INPUT':
                    if (el.type === 'text' || el.type === 'search') {
                        el.value = '';
                        el.dispatchEvent(new Event('input', { bubbles: true }));
                    } else if (el.type === 'checkbox' || el.type === 'radio') {
                        el.checked = false;
                        el.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    break;
                case 'TEXTAREA':
                    el.value = '';
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                    break;
            }
        });

        this.applyFilter();

        if (typeof saveFilterState === 'function') {
            saveFilterState();
        } else if (window.saveFilterState) {
            window.saveFilterState();
        }
    }

    uniqueBy(array, key) {
        return [...new Map(array.map(item => [item[key], item])).values()];
    }

    populateSelect(select, items = [], valueKey, textKey, placeholderText = 'Selecione...') {
        if (!select) return;

        const currentValue = String(select.value ?? '');
        select.innerHTML = '';

        const fragment = document.createDocumentFragment();
        const validItems = items.filter(item => item[valueKey] !== null && item[valueKey] !== undefined);

        if (validItems.length > 0) {
            const placeholderOption = new Option(placeholderText, '');
            fragment.appendChild(placeholderOption);

            validItems
                .sort((a, b) => String(a[textKey]).localeCompare(String(b[textKey]), 'pt-BR', { numeric: true }))
                .forEach(item => {
                    const value = String(item[valueKey]);
                    const option = new Option(item[textKey], value);

                    if (value === currentValue) {
                        option.selected = true;
                        placeholderOption.selected = false;
                    }

                    fragment.appendChild(option);
                });

            select.disabled = false;
        } else {
            fragment.appendChild(new Option(placeholderText, ''));
            select.disabled = true;
        }

        select.appendChild(fragment);
    }

    getCurrentParams() {
        const params = {};

        this.fields.forEach(f => {
            if (!f.element) return;
            const value = f.element.value?.trim();
            if (value) params[f.key] = value;
        });

        const searchInput = document.getElementById('top-nav-search-input');
        if (searchInput?.value.trim()) {
            params.search = searchInput.value.trim();
        }

        return params;
    }

    applyFilter(changedField) {
        if (!Array.isArray(this.filterData)) this.filterData = [];
        let filtered = [...this.filterData];

        this.fields.forEach(f => {
            if (!f.element) return;
            const val = f.element.value?.trim();
            if (val !== undefined && val !== null && val !== '') {
                filtered = filtered.filter(d => String(d[f.key]) === String(val));
            }
        });

        this.fields.forEach(f => {
            if (!f.element) return;
            if (!changedField || f !== changedField) {
                if (f.element.tagName === 'SELECT') {
                    let filteredForSelect = [...this.filterData];

                    this.fields.forEach(other => {
                        if (!other.element || other === f) return;
                        const val = other.element.value?.trim();
                        if (val !== undefined && val !== null && val !== '') {
                            filteredForSelect = filteredForSelect.filter(d => String(d[other.key]) === String(val));
                        }
                    });

                    const currentVal = f.element.value;
                    if (currentVal && !filteredForSelect.some(d => String(d[f.key]) === currentVal)) {
                        const original = this.filterData.find(d => String(d[f.key]) === currentVal);
                        if (original) filteredForSelect.push(original);
                    }

                    const uniqueItems = this.uniqueBy(filteredForSelect, f.key)
                        .map(item => ({
                            value: item[f.key],
                            label: f.formatLabel ? f.formatLabel(item[f.key]) : item[f.key]
                        }));

                    console.log(`Populando select ${f.key}`);
                    console.log('Valores filtrados:', filteredForSelect.map(d => d[f.key]));
                    this.populateSelect(f.element, uniqueItems, 'value', 'label', f.placeholder);
                }
            }
        });

        const params = this.getCurrentParams();
        this.onParamsChange?.(params);
        document.dispatchEvent(new CustomEvent('filtersUpdated', { detail: params }));
    }

    updateData(newData) {
        this.filterData = Array.isArray(newData) ? newData : [];
        if (this.fields.some(f => f.element)) {
            this.applyFilter();
        }
    }
}
