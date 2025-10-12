document.addEventListener('DOMContentLoaded', () => {
    const btnFilter = document.getElementById('btn-filter');
    const popupFilter = document.getElementById('popup-filter');

    btnFilter.addEventListener('click', async () => {
        const wasOpen = popupFilter.style.display === 'block';

        if (wasOpen) {
            popupFilter.style.display = 'none';
            popupFilter.innerHTML = '';
            return;
        }

        const filterUrl = btnFilter.dataset.filterUrl;
        if (!filterUrl) {
            console.error('URL do filtro não definida no botão');
            return;
        }

        const savedState = loadFilterState();

        try {
            const response = await fetch(filterUrl);
            if (!response.ok) throw new Error('Erro ao carregar o filtro');

            const html = await response.text();
            popupFilter.innerHTML = html;

            const rect = btnFilter.getBoundingClientRect();
            popupFilter.style.top = rect.bottom + window.scrollY + 'px';
            popupFilter.style.left = rect.left + window.scrollX + 'px';
            popupFilter.style.display = 'block';

            await new Promise(r => setTimeout(r, 0));
        } catch (error) {
            console.error(error);
            popupFilter.innerHTML = '<p>Não foi possível carregar o filtro.</p>';
            popupFilter.style.display = 'block';
        }
    });

    document.addEventListener('click', (event) => {
        if (!popupFilter.contains(event.target) && event.target !== btnFilter) {
            popupFilter.style.display = 'none';
        }
    });

    const searchInput = document.getElementById('top-nav-search-input');

    if (searchInput) {
        let timeout = null;

        searchInput.addEventListener('input', () => {
            clearTimeout(timeout);

            timeout = setTimeout(() => {
                const searchTerm = searchInput.value.trim();
                const event = new CustomEvent('topSearch', { detail: { search: searchTerm } });
                document.dispatchEvent(event);
            }, 300);
        });
    }

    function saveFilterState() {
        const state = {
            course: document.getElementById('filter-course')?.value || '',
            period: document.getElementById('filter-period')?.value || '',
            term: document.getElementById('filter-term')?.value || '',
            can_borrow: document.getElementById('filter-status')?.value || '',
        };
        sessionStorage.setItem('studentsFilterState', JSON.stringify(state));
    }

    function loadFilterState() {
        const raw = sessionStorage.getItem('studentsFilterState');
        return raw ? JSON.parse(raw) : {};
    }
});
