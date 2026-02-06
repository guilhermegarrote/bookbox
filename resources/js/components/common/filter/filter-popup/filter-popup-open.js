/**
 * Filter Popup Open Logic
 * -----------------------
 * Main popup initialization logic: fetch, sanitize, restore values,
 * bind events, and apply filters.
 */

import { btnFilter, popupFilter, showPopup } from './filter-popup-dom.js';
import { fetchFilterHTML } from './filter-popup-fetch.js';
import { sanitizeHTML } from './filter-popup-sanitize.js';
import { positionPopup } from './filter-popup-position.js';

/**
 * Opens the filter popup and initializes its UI.
 *
 * @param {Object} filterUIInstance - FilterUI instance responsible for filtering logic.
 * @returns {Promise<void>}
 */
export const openFilterPopup = async filterUIInstance => {
    const filterUrl = btnFilter?.dataset.filterUrl;
    if (!filterUrl || !popupFilter) return;

    try {
        const html = await fetchFilterHTML(filterUrl);

        popupFilter.innerHTML = '';
        popupFilter.appendChild(sanitizeHTML(html));

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

            // restore saved value
            if (
                f.element.tagName === 'SELECT' ||
                (f.element.tagName === 'INPUT' &&
                    f.element.type !== 'checkbox' &&
                    f.element.type !== 'radio')
            ) {
                f.element.value = savedValue;
            } else if (f.element.type === 'checkbox' || f.element.type === 'radio') {
                f.element.checked = !!savedValue;
            }

            // remove old listeners safely
            f.element.replaceWith(f.element.cloneNode(true));
            f.element = document.getElementById(`filter-${f.key}`);

            // restore again after cloning
            if (
                f.element.tagName === 'SELECT' ||
                (f.element.tagName === 'INPUT' &&
                    f.element.type !== 'checkbox' &&
                    f.element.type !== 'radio')
            ) {
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
            clearBtn.onclick = e => {
                e.preventDefault();
                e.stopPropagation();
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
