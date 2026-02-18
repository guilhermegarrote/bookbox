/**
 * Handles infinite scroll loading for a table.
 */
export default class InfiniteScroll {
    /**
     * @param {Object} manager Table manager instance.
     */
    constructor(manager) {
        this.manager = manager;
        this.sentinel = null;
        this.observer = null;
        this.initInfiniteScroll();
    }

    /**
     * Initializes the IntersectionObserver and sets up the sentinel element.
     * @returns {void}
     */
    initInfiniteScroll() {
        const wrapper = this.manager.tableContainer;
        if (!wrapper) return;

        this.observer = new IntersectionObserver(
            async ([entry]) => {
                if (entry.isIntersecting && !this.manager.isLoading && this.manager.hasMore) {
                    await this.manager.dataModule.updateTable({}, true);
                    this.updateSentinel();
                }
            },
            {
                root: wrapper,
                rootMargin: '100px',
                threshold: 0
            }
        );

        this.updateSentinel();

        this.onDataUpdated = () => this.updateSentinel();
        window.addEventListener('tableDataUpdated', this.onDataUpdated);
    }

    /**
     * Creates or removes the sentinel element based on the availability of more data.
     * @returns {void}
     */
    updateSentinel() {
        const wrapper = this.manager.tableContainer;

        if (!this.manager.hasMore && this.sentinel) {
            this.destroy();
            return;
        }

        if (this.manager.hasMore && !this.sentinel) {
            const sentinel = document.createElement('div');
            sentinel.id = 'infinite-scroll-sentinel';
            sentinel.style.height = '1px';
            sentinel.style.width = '100%';
            wrapper.appendChild(sentinel);
            this.observer.observe(sentinel);
            this.sentinel = sentinel;
        }
    }

    /**
     * Cleans up the IntersectionObserver and event listener,
     * and removes the sentinel element from the DOM.
     * @returns {void}
     */
    destroy() {
        if (this.sentinel) this.observer.unobserve(this.sentinel);
        if (this.observer) this.observer.disconnect();
        if (this.onDataUpdated) window.removeEventListener('tableDataUpdated', this.onDataUpdated);
    }
}
