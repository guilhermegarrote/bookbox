export function initStudentsFilterUI(filterData, onParamsChange) {
    const courseSelect = document.getElementById('filter-course');
    const periodSelect = document.getElementById('filter-period');
    const termSelect = document.getElementById('filter-term');
    const statusSelect = document.getElementById('filter-status');
    const searchInput = document.getElementById('top-nav-search-input');

    if (!courseSelect || !periodSelect || !termSelect) {
        console.warn('Filtros não encontrados no DOM.');
        return;
    }

    function uniqueBy(array, key) {
        return [...new Map(array.map(item => [item[key], item])).values()];
    }

    function populateSelect(select, items, valueKey, textKey, placeholderText) {
        const currentValue = select.value;
        select.innerHTML = '';

        const placeholderOption = new Option(placeholderText, '');
        select.appendChild(placeholderOption);

        items.forEach(item => {
            const option = new Option(item[textKey], item[valueKey]);
            select.appendChild(option);
        });

        if (currentValue === '') {
            select.selectedIndex = 0;
        } else if (items.some(i => i[valueKey] === currentValue)) {
            select.value = currentValue;
        } else {
            select.selectedIndex = 0;
        }

        select.disabled = false;
    }

    function getCurrentParams() {
        const params = {
            course: courseSelect.value,
            period: periodSelect.value,
            term: termSelect.value,
            can_borrow: statusSelect?.value || '',
            search: searchInput?.value.trim() || ''
        };

        Object.keys(params).forEach(key => {
            if (!params[key]) {
                delete params[key];
            }
        });

        return params;
    }

    function updateFilters(changedSelect) {
        const selectedCourse = courseSelect.value;
        const selectedPeriod = periodSelect.value;
        const selectedTerm = termSelect.value;

        let filtered = filterData;
        if (selectedCourse) filtered = filtered.filter(d => d.course === selectedCourse);
        if (selectedPeriod) filtered = filtered.filter(d => d.period === selectedPeriod);
        if (selectedTerm) filtered = filtered.filter(d => d.term === selectedTerm);

        if (!selectedCourse) {
            const allPeriods = uniqueBy(filterData, 'period')
                .map(p => ({ value: String(p.period), label: `${p.period}°` }));
            populateSelect(periodSelect, allPeriods, 'value', 'label', 'Período');

            const allTerms = uniqueBy(filterData, 'term')
                .map(t => ({
                    value: t.term,
                    label: t.term.toLowerCase() === 'annual' ? 'Anual' : 'Semestral'
                }));
            populateSelect(termSelect, allTerms, 'value', 'label', 'Regime');
        }

        if (changedSelect !== courseSelect) {
            const courses = uniqueBy(filterData, 'course').map(c => ({ value: c.course, label: c.course }));
            populateSelect(courseSelect, courses, 'value', 'label', 'Curso');
        }

        if (changedSelect !== periodSelect) {
            const periods = uniqueBy(
                selectedCourse ? filterData.filter(d => d.course === selectedCourse) : filterData,
                'period'
            ).map(p => ({ value: p.period, label: `${p.period}°` }));
            populateSelect(periodSelect, periods, 'value', 'label', 'Período');
        }

        if (changedSelect !== termSelect) {
            const terms = uniqueBy(
                selectedCourse ? filterData.filter(d => d.course === selectedCourse) : filterData,
                'term'
            ).map(t => ({
                value: t.term,
                label: t.term.toLowerCase() === 'annual' ? 'Anual' : 'Semestral'
            }));
            populateSelect(termSelect, terms, 'value', 'label', 'Regime');
        }

        if (onParamsChange) onParamsChange(getCurrentParams());

        document.dispatchEvent(new CustomEvent('filtersUpdated', {
            detail: getCurrentParams()
        }));
    }

    courseSelect.addEventListener('change', () => updateFilters(courseSelect));
    periodSelect.addEventListener('change', () => {
        updateFilters(periodSelect);
        if (onParamsChange) onParamsChange(getCurrentParams());
    });
    termSelect.addEventListener('change', () => {
        updateFilters(termSelect);
        if (onParamsChange) onParamsChange(getCurrentParams());
    });

    if (statusSelect) {
        statusSelect.addEventListener('change', () => {
            if (onParamsChange) onParamsChange(getCurrentParams());
        });
    }

    if (searchInput) {
        searchInput.addEventListener('change', () => {
            if (onParamsChange) onParamsChange(getCurrentParams());
        });

        searchInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter' && onParamsChange) {
                onParamsChange(getCurrentParams());
            }
        });
    }

    updateFilters();
}
