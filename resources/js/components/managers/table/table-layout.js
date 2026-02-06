/**
 * Handles table layout calculations and resize updates.
 */
export default class TableLayout {
    /**
     * @param {Object} manager Table manager instance.
     */
    constructor(manager) {
        this.manager = manager;

        this.resizeObserver = null;

        this.initResizeObserver();
    }

    /**
     * Calculates how many rows should be displayed based on wrapper height.
     *
     * @returns {number}
     */
    calculateVisibleRows() {
        const wrapper = document.querySelector('.table-wrapper');
        if (!wrapper) return 15;

        const wrapperRect = wrapper.getBoundingClientRect();
        let wrapperInnerHeight = wrapper.clientHeight || wrapperRect.height;

        const wStyles = window.getComputedStyle(wrapper);
        wrapperInnerHeight -= parseFloat(wStyles.paddingTop) || 0;
        wrapperInnerHeight -= parseFloat(wStyles.paddingBottom) || 0;

        const table = wrapper.querySelector('table.data-table') || wrapper.querySelector('table');
        const thead = table ? table.querySelector('thead') : null;
        const theadHeight = thead ? thead.getBoundingClientRect().height : 0;

        const availableForRows = Math.max(0, wrapperInnerHeight - theadHeight);

        let row = table ? table.querySelector('tbody tr') : null;
        let createdTempRow = false;

        if (!row && table) {
            const tbody = table.querySelector('tbody');

            if (tbody) {
                createdTempRow = true;

                const tr = document.createElement('tr');
                const thCount = table.querySelectorAll('thead th').length || 6;

                for (let i = 0; i < thCount; i++) {
                    const td = document.createElement('td');
                    td.innerHTML = 'X';
                    tr.appendChild(td);
                }

                tr.style.visibility = 'hidden';
                tr.style.position = 'hidden';
                tr.style.pointerEvents = 'none';

                tbody.appendChild(tr);
                row = tr;
            }
        }

        const rowHeight = row ? row.getBoundingClientRect().height || row.offsetHeight : 42;

        if (createdTempRow && row && row.parentElement) {
            row.parentElement.removeChild(row);
        }

        const visibleRows = rowHeight > 0 ? Math.round(availableForRows / rowHeight) : 0;
        return visibleRows >= 5 ? visibleRows * 2 : 15;
    }

    /**
     * Initializes ResizeObserver to update table pagination on resize.
     */
    initResizeObserver() {
        const wrapper = document.querySelector('.table-wrapper');
        if (!wrapper) return;

        this.resizeObserver = new ResizeObserver(() => {
            const newPerPage = this.calculateVisibleRows();

            if (Math.abs(newPerPage - this.manager.perPage) >= 2) {
                this.manager.perPage = newPerPage;
                this.manager.resetPagination();
                this.manager.dataModule.updateTable({}, false);
            }
        });

        this.resizeObserver.observe(wrapper);
    }
}
