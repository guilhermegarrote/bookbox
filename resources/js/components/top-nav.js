document.addEventListener('DOMContentLoaded', () => {
    const btnFilter = document.getElementById('btn-filter');
    const popupFilter = document.getElementById('popup-filter');
    const searchInput = document.getElementById('top-nav-search-input');

    const filterUIInitializer = window.App?.initFilterUI;
    const updateTable = window.App?.updateTable;
    const filterStateKey = window.App?.filterStateKey || 'defaultFilterState';

    const loadFilterState = () => {
        try {
            const raw = sessionStorage.getItem(filterStateKey);
            return raw ? JSON.parse(raw) : {};
        } catch {
            return {};
        }
    };

    const saveFilterState = () => {
        const state = {};

        if (window.filterUIInstance?.fields) {
            window.filterUIInstance.fields.forEach(f => {
                const el = document.getElementById(`filter-${f.key}`);
                if (!el) return;

                if (el.type === 'checkbox' || el.type === 'radio') {
                    state[f.key] = el.checked;
                } else {
                    state[f.key] = el.value;
                }
            });
        }

        sessionStorage.setItem(filterStateKey, JSON.stringify(state));
    };

    const applySavedState = (state) => {
        Object.entries(state).forEach(([key, val]) => {
            const el = document.getElementById(`filter-${key}`);
            if (!el) return;

            if (el.type === 'checkbox' || el.type === 'radio') {
                el.checked = val === true || val === 'true';
            } else {
                el.value = val;
            }
            el.dispatchEvent(new Event('change', { bubbles: true }));
        });
    };

    const sanitizeHTML = (html) => {
        const template = document.createElement('template');
        template.innerHTML = html;
        template.content.querySelectorAll('script, iframe, object, [onload], [onclick], [onerror]').forEach(el => el.remove());
        return template.content;
    };

    const fetchFilterHTML = async (url) => {
        const response = await fetch(url, { credentials: 'same-origin' });
        if (!response.ok) throw new Error('Erro ao carregar o filtro');
        return response.text();
    };

    const positionPopup = () => {
        const rect = btnFilter.getBoundingClientRect();
        popupFilter.style.top = `${rect.bottom + window.scrollY}px`;
        popupFilter.style.left = `${rect.left + window.scrollX}px`;
    };

    const showPopup = () => popupFilter.classList.add('visible');

    const hidePopup = () => {
        saveFilterState();
        popupFilter.classList.remove('visible');
        popupFilter.innerHTML = '';
    };

    const openFilterPopup = async () => {
        const filterUrl = btnFilter?.dataset.filterUrl;
        if (!filterUrl) return;

        try {
            const html = await fetchFilterHTML(filterUrl);
            popupFilter.innerHTML = '';
            popupFilter.appendChild(sanitizeHTML(html));
            positionPopup();

            if (window.filterUIInstance?.fields) {
                window.filterUIInstance.fields.forEach(f => {
                    f.element = document.getElementById(`filter-${f.key}`);
                });
                window.filterUIInstance.init();
            }

            const savedState = loadFilterState();
            applySavedState(savedState);

            showPopup();
        } catch (err) {
            console.error(err);
            popupFilter.innerHTML = '<p>Não foi possível carregar o filtro.</p>';
            showPopup();
        }
    };

    if (filterUIInitializer && updateTable && !window.filterUIInstance) {
        window.filterUIInstance = filterUIInitializer();
        applySavedState(loadFilterState());
    }

    btnFilter?.addEventListener('click', () => {
        popupFilter.classList.contains('visible') ? hidePopup() : openFilterPopup();
    });

    document.addEventListener('click', (event) => {
        if (event.target.closest('#filter-clean-btn')) {
            window.filterUIInstance?.clearAllFilters();
        } else if (!popupFilter.contains(event.target) && event.target !== btnFilter) {
            hidePopup();
        }
    });

    if (searchInput) {
        let timeout = null;
        let lastRequestId = 0;

        searchInput.addEventListener('input', () => {
            clearTimeout(timeout);
            timeout = setTimeout(async () => {
                const searchTerm = searchInput.value.trim();
                const requestId = ++lastRequestId;

                if (updateTable && window.filterUIInstance) {
                    try {
                        const newData = await updateTable({ search: searchTerm });
                        if (requestId === lastRequestId && newData.filterData) {
                            window.filterUIInstance.updateData(newData.filterData);
                        }
                    } catch (err) {
                        console.error("Search update failed:", err);
                    }
                }
            }, 300);
        });
    }
});
