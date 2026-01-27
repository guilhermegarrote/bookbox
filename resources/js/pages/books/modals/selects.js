export function initBooksSelects(filterData) {
    const genreNameSelect = document.getElementById('genre_name');
    if (!genreNameSelect) return;

    const populateSelect = (select, items, valueKey, textKey, placeholder, presetValue) => {
        select.innerHTML = '';
        select.appendChild(new Option(placeholder, ''));

        items.forEach(item => {
            select.appendChild(new Option(item[textKey], item[valueKey]));
        });

        const currentValue = presetValue || select.dataset.value || '';
        if (currentValue && items.some(i => i[valueKey] == currentValue)) {
            select.value = currentValue;
        }
    };

    function updateSelects(selected = {}) {
        const selectedGenreName =
            selected.genre_name ??
            genreNameSelect.dataset.value ??
            '';

        const genreNames = [...new Set(filterData.map(i => i.genre_name))]
            .filter(Boolean)
            .map(name => ({ value: name, label: name }));

        populateSelect(
            genreNameSelect,
            genreNames,
            'value',
            'label',
            'Gênero',
            selectedGenreName
        );
    }

    updateSelects({ genre_name: genreNameSelect.value });
}
