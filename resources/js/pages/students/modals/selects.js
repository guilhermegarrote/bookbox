export function initStudentsSelects(filterData) {
    const courseSelect = document.getElementById('course');
    const periodSelect = document.getElementById('period');
    const termSelect = document.getElementById('term');
    if (!courseSelect || !periodSelect || !termSelect) return;

    const uniqueBy = (array, key) =>
        [...new Map(array.map(item => [item[key], item])).values()];

    const populateSelect = (select, items, valueKey, textKey, placeholder, presetValue) => {
        select.innerHTML = '';
        select.appendChild(new Option(placeholder, ''));
        items.forEach(item => select.appendChild(new Option(item[textKey], item[valueKey])));

        const initial = presetValue || select.dataset.value || '';
        if (initial && items.some(i => i[valueKey] == initial)) {
            select.value = initial;
        } else {
            select.selectedIndex = 0;
        }
        select.disabled = false;
    };

    function updateSelects(selected = {}) {
        const selectedCourse = selected.course || courseSelect.value || courseSelect.dataset.value || '';
        const selectedPeriod = selected.period || periodSelect.value || periodSelect.dataset.value || '';
        const selectedTerm = selected.term || termSelect.value || termSelect.dataset.value || '';

        const courses = uniqueBy(filterData, 'course').map(c => ({ value: c.course, label: c.course }));
        populateSelect(courseSelect, courses, 'value', 'label', 'Curso', selectedCourse);

        const filtered = selectedCourse ? filterData.filter(d => d.course === selectedCourse) : filterData;

        const periods = uniqueBy(filtered, 'period').map(p => ({ value: p.period, label: `${p.period}°` }));
        populateSelect(periodSelect, periods, 'value', 'label', 'Período', selectedPeriod);

        const terms = uniqueBy(filtered, 'term').map(t => ({
            value: t.term,
            label: t.term.toLowerCase() === 'annual' ? 'Anual' : 'Semestral'
        }));
        populateSelect(termSelect, terms, 'value', 'label', 'Regime', selectedTerm);
    }

    courseSelect.addEventListener('change', () => updateSelects({ course: courseSelect.value }));
    periodSelect.addEventListener('change', () => updateSelects({
        course: courseSelect.value,
        period: periodSelect.value
    }));
    termSelect.addEventListener('change', () => updateSelects({
        course: courseSelect.value,
        term: termSelect.value
    }));

    updateSelects();
}
