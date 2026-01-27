export default class Events {
    constructor(manager) {
        this.manager = manager;

        this.filtersTimeout = null;

        this.bindGlobalEvents();
    }

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
