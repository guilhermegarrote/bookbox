export class FilterUI {
    constructor({ filterData = [], fields = [], onParamsChange = () => { } }) {
        this.filterData = Array.isArray(filterData) ? filterData : [];
        this.fields = fields.map(f => ({ ...f }));
        this.onParamsChange = onParamsChange;

        if (!window.App.filterUIInstance) window.App.filterUIInstance = this;
    }

    init() {
        let savedFilters = {};
        try {
            savedFilters = JSON.parse(localStorage.getItem('lastFilters') || '{}');
        } catch (e) {
            console.warn('Failed to parse saved filters from localStorage', e);
        }

        this.fields.forEach(field => {
            if (!field.element) field.element = document.getElementById(field.key);
            if (!field.element) return;

            if (field.element.tagName === 'SELECT') {
                field.optionsData = this.uniqueBy(this.filterData, field.key)
                    .map(item => ({ value: item[field.key], label: f.formatLabel ? f.formatLabel(item[field.key]) : item[field.key] }));

                if (savedFilters[field.key] !== undefined) field.element.value = savedFilters[field.key];

                this.populateSelect(field.element, field.optionsData, 'value', 'label', field.placeholder);
            }

            field.element.addEventListener('change', () => {
                this.applyFilter(field);
                this.saveFilterState();
            });

            if (field.element.tagName === 'INPUT') {
                if (savedFilters[field.key] !== undefined) field.element.value = savedFilters[field.key];
                field.element.addEventListener('keyup', e => {
                    if (e.key === 'Enter') {
                        this.applyFilter(field);
                        this.saveFilterState();
                    }
                });
            }
        });

        if (this.fields.some(f => f.element)) this.applyFilter();
    }

    saveFilterState() {
        try {
            localStorage.setItem('lastFilters', JSON.stringify(this.getCurrentParams()));
        } catch (e) {
            console.warn('Failed to save filter state to localStorage', e);
        }
    }

    clearAllFilters() {
        this.fields.forEach(f => {
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

        localStorage.removeItem('lastFilters');

        this.fields.forEach(f => {
            if (!f.element || f.element.tagName !== 'SELECT') return;

            const allOptions = this.uniqueBy(this.filterData, f.key)
                .map(item => ({
                    value: item[f.key],
                    label: f.formatLabel ? f.formatLabel(item[f.key]) : item[f.key]
                }))
                .sort((a, b) => String(a.label).localeCompare(String(b.label), 'pt-BR', { numeric: true }));

            this.populateSelect(f.element, allOptions, 'value', 'label', f.placeholder);
            f.element.value = '';
        });

        const emptyParams = {};
        this.onParamsChange?.(emptyParams);
        document.dispatchEvent(new CustomEvent('filtersUpdated', { detail: emptyParams }));

        this.refreshTable();
    }

    uniqueBy(array, key) {
        return [...new Map(array.map(item => [item[key], item])).values()];
    }

    populateSelect(select, items = [], valueKey, textKey, placeholderText = 'Selecione...') {
        if (!select) return;

        const currentValue = select.value || '';
        select.innerHTML = '';

        const fragment = document.createDocumentFragment();

        const placeholderOption = new Option(placeholderText, '');
        fragment.appendChild(placeholderOption);

        const uniqueItems = Array.from(new Set(items.map(item => item[valueKey])))
            .map(value => {
                const item = items.find(i => i[valueKey] === value);
                return {
                    value: item[valueKey],
                    label: item[textKey] ?? item[valueKey]
                };
            })
            .sort((a, b) => String(a.label).localeCompare(String(b.label), 'pt-BR', { numeric: true }));

        uniqueItems.forEach(item => {
            const option = new Option(item.label, item.value);
            if (item.value === currentValue) option.selected = true;
            fragment.appendChild(option);
        });

        select.disabled = uniqueItems.length === 0;
        select.appendChild(fragment);
    }

    getCurrentParams() {
        const params = {};
        this.fields.forEach(f => {
            if (!f.element) return;
            const val = f.element.value?.trim();
            if (val) params[f.key] = val;
        });

        const searchInput = document.getElementById('top-nav-search-input');
        const searchVal = searchInput?.value?.trim();
        if (searchVal) params.search = searchVal;

        return params;
    }

    applyFilter(changedField) {
        if (!Array.isArray(this.filterData)) this.filterData = [];
        let filtered = [...this.filterData];

        this.fields.forEach(f => {
            if (!f.element) return;
            const val = f.element.value?.trim();
            if (val) filtered = filtered.filter(d => String(d[f.key]) === String(val));
        });

        this.fields.forEach(f => {
            if (!f.element) return;
            if (!changedField || f !== changedField) {
                if (f.element.tagName === 'SELECT') {
                    let filteredForSelect = [...this.filterData];
                    this.fields.forEach(other => {
                        if (!other.element || other === f) return;
                        const val = other.element.value?.trim();
                        if (val) filteredForSelect = filteredForSelect.filter(d => String(d[other.key]) === String(val));
                    });

                    const currentVal = f.element.value;
                    if (currentVal && !filteredForSelect.some(d => String(d[f.key]) === currentVal)) {
                        const original = this.filterData.find(d => String(d[f.key]) === currentVal);
                        if (original) filteredForSelect.push(original);
                    }

                    const uniqueItems = this.uniqueBy(filteredForSelect, f.key)
                        .map(item => ({ value: item[f.key], label: f.formatLabel ? f.formatLabel(item[f.key]) : item[f.key] }));

                    this.populateSelect(f.element, uniqueItems, 'value', 'label', f.placeholder);
                }
            }
        });

        const params = this.getCurrentParams();
        this.onParamsChange?.(params);
        document.dispatchEvent(new CustomEvent('filtersUpdated', { detail: params }));

        this.refreshTable();
    }

    updateData(newData, applyFilter = false) {
        const isSame = JSON.stringify(newData) === JSON.stringify(this.filterData);
        this.filterData = Array.isArray(newData) ? newData : [];
        if (applyFilter && !isSame && this.fields.some(f => f.element)) this.applyFilter();
    }

    initSelectsWithCurrentData() {
        this.fields.forEach(field => {
            if (!field.element || field.element.tagName !== 'SELECT') return;

            const savedFilters = this.getSavedFilters();
            const currentValue = savedFilters[field.key] || '';

            // Gerar opções com base nos dados atuais
            const optionsData = this.uniqueBy(this.filterData, field.key)
                .map(item => ({
                    value: item[field.key],
                    label: field.formatLabel ? field.formatLabel(item[field.key]) : item[field.key]
                }));

            this.populateSelect(field.element, optionsData, 'value', 'label', field.placeholder);

            // Restaurar valor
            if (currentValue && optionsData.some(opt => opt.value == currentValue)) {
                field.element.value = currentValue;
            }
        });
    }

    getSavedFilters() {
        try {
            return JSON.parse(localStorage.getItem('lastFilters') || '{}');
        } catch {
            return {};
        }
    }

    refreshTable() {
        // Se você tem uma função global para carregar a tabela:
        if (typeof window.loadTable === 'function') {
            window.loadTable();
            return;
        }

        // Ou se usa fetch com params:
        const params = this.getCurrentParams();
        const url = new URL(window.location);
        Object.keys(params).forEach(key => {
            if (params[key]) {
                url.searchParams.set(key, params[key]);
            } else {
                url.searchParams.delete(key);
            }
        });

        // Recarregar página com novos params (ou fetch)
        history.replaceState(null, '', url);
        // Se for SPA, chame sua função de busca:
        if (typeof window.searchWithFilters === 'function') {
            window.searchWithFilters();
        }
    }
}
