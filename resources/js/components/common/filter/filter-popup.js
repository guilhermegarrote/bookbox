export const btnFilter = document.getElementById('btn-filter');
const popupFilter = document.getElementById('popup-filter');

const sanitizeHTML = html => {
    const template = document.createElement('template');
    template.innerHTML = html;
    template.content.querySelectorAll('script, iframe, object, [onload], [onclick], [onerror]').forEach(el => el.remove());
    return template.content;
};

const fetchFilterHTML = async url => {
    const response = await fetch(url, { credentials: 'same-origin' });
    if (!response.ok) throw new Error('Erro ao carregar o filtro');
    return response.text();
};

const positionPopup = () => {
    const rect = btnFilter.getBoundingClientRect();
    popupFilter.style.top = `${rect.bottom + window.scrollY}px`;
    popupFilter.style.left = `${rect.left + window.scrollX}px`;
};

export const showPopup = () => popupFilter.classList.add('visible');
export const hidePopup = () => {
    popupFilter.classList.remove('visible');
    popupFilter.innerHTML = '';
};

export const openFilterPopup = async (filterUIInstance) => {
    const filterUrl = btnFilter?.dataset.filterUrl;
    if (!filterUrl) return;

    try {
        const html = await fetchFilterHTML(filterUrl);
        popupFilter.innerHTML = '';
        const content = sanitizeHTML(html);
        popupFilter.appendChild(content);
        positionPopup();

        let savedFilters = {};
        try {
            savedFilters = JSON.parse(localStorage.getItem('lastFilters') || '{}');
        } catch (e) {
            console.warn('Erro ao ler filtros salvos', e);
        }

        filterUIInstance.fields.forEach(f => {
            const newElement = document.getElementById(`filter-${f.key}`);
            if (!newElement) return;

            f.element = newElement;

            const savedValue = savedFilters[f.key] || '';
            if (f.element.tagName === 'SELECT' || (f.element.tagName === 'INPUT' && f.element.type !== 'checkbox' && f.element.type !== 'radio')) {
                f.element.value = savedValue;
            } else if (f.element.type === 'checkbox' || f.element.type === 'radio') {
                f.element.checked = !!savedValue;
            }

            f.element.replaceWith(f.element.cloneNode(true));
            f.element = document.getElementById(`filter-${f.key}`);

            if (f.element.tagName === 'SELECT' || (f.element.tagName === 'INPUT' && f.element.type !== 'checkbox' && f.element.type !== 'radio')) {
                f.element.value = savedValue;
            } else if (f.element.type === 'checkbox' || f.element.type === 'radio') {
                f.element.checked = !!savedValue;
            }

            f.element.addEventListener('change', () => {
                filterUIInstance.applyFilter(f);
                filterUIInstance.saveFilterState();
            });

            if (f.element.tagName === 'INPUT') {
                f.element.addEventListener('keyup', e => {
                    if (e.key === 'Enter') {
                        filterUIInstance.applyFilter(f);
                        filterUIInstance.saveFilterState();
                    }
                });
            }
        });

        filterUIInstance.initSelectsWithCurrentData();
        filterUIInstance.applyFilter();

        const clearBtn = popupFilter.querySelector('#filter-clean-btn');

        if (clearBtn) {
            clearBtn.onclick = (e) => {
                e.preventDefault();
                e.stopPropagation()
                filterUIInstance.clearAllFilters();
            };
        }

        showPopup();
    } catch (err) {
        console.error(err);
        popupFilter.innerHTML = '<p>Não foi possível carregar o filtro.</p>';
        showPopup();
    }
};

