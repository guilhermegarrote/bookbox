/**
 * Handles table sorting behavior.
 */
export default class Sorting {
    /**
     * @param {Object} manager Table manager instance.
     */
    constructor(manager) {
        /** @type {Object} */
        this.manager = manager;

        this.initSorting();
    }

    /**
     * Binds click events for sortable table headers.
     */
    initSorting() {
        document.addEventListener('click', async (e) => {
            const th = e.target.closest('.sort-link');
            if (!th) return;

            const m = this.manager;
            const column = th.dataset.sort;

            if (m.sortColumn === column) {
                m.sortDirection = m.sortDirection === "asc" ? "desc" : "asc";
            } else {
                m.sortColumn = column;
                m.sortDirection = "asc";
            }

            m.resetPagination();
            await m.dataModule.updateTable(undefined, false);
        });
    }

    /**
     * Updates the sorting icons in the table headers.
     */
    updateSortIcons() {
        const m = this.manager;

        document.querySelectorAll('.sort-link').forEach(th => {
            const col = th.dataset.sort;
            let icon = '';

            if (col === m.sortColumn) {
                icon = m.sortDirection === 'asc' ? '↑' : '↓';
            }

            let span = th.querySelector('span');
            if (!span) {
                const originalLabel = th.dataset.label || th.textContent.trim();
                th.dataset.label = originalLabel;
                th.innerHTML = `<span class="sort-text">${originalLabel}</span><span class="sort-icon">${icon}</span>`;
            }

            const iconSpan = th.querySelector('.sort-icon');
            if (iconSpan) {
                iconSpan.textContent = icon;
            }
        });
    }
}
