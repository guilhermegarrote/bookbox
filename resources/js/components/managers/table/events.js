/**
 * Handles global UI events for the table manager.
 */
export default class Events {
    /**
     * @param {Object} manager Table manager instance.
     */
    constructor(manager) {
        this.manager = manager;
        this.filtersTimeout = null;
        this.resizeTimeout = null;

        this.bindGlobalEvents();
    }

    /**
     * Binds global listeners for filters update and window resize.
     */
    bindGlobalEvents() {
        const m = this.manager;

        document.addEventListener('filtersUpdated', (e) => {
            const params = e.detail || {};

            clearTimeout(this.filtersTimeout);

            this.filtersTimeout = setTimeout(() => {
                m.resetPagination();
                m.currentParams = { ...params };
                m.dataModule.updateTable(m.currentParams, false);
            }, 200);
        });

        window.addEventListener('resize', () => {
            clearTimeout(this.resizeTimeout);

            this.resizeTimeout = setTimeout(() => {
                const newPerPage = m.layoutModule.calculateVisibleRows();

                if (Math.abs(newPerPage - m.perPage) >= 2) {
                    m.perPage = newPerPage;
                    m.resetPagination();
                    m.dataModule.updateTable(m.currentParams, false);
                }
            }, 200);
        });
    }
}
