/**
 * Handles infinite scroll loading for the table.
 */
export default class InfiniteScroll {
    /**
     * @param {Object} manager Table manager instance.
     */
    constructor(manager) {
        this.manager = manager;

        this.initInfiniteScroll();
    }

    /**
     * Initializes IntersectionObserver to load more table data when reaching the bottom.
     */
    initInfiniteScroll() {
        const m = this.manager;
        const wrapper = m.tableContainer;
        if (!wrapper) return;

        /**
         * Creates and appends the sentinel element used to detect scroll end.
         *
         * @returns {HTMLElement|null}
         */
        const createSentinel = () => {
            if (!m.hasMore) return null;

            const sentinel = document.createElement('div');
            sentinel.id = 'infinite-scroll-sentinel';
            sentinel.style.height = '1px';
            sentinel.style.width = '100%';

            wrapper.appendChild(sentinel);
            return sentinel;
        };

        let sentinel = createSentinel();

        const observer = new IntersectionObserver(
            async ([entry]) => {
                if (entry.isIntersecting && !m.isLoading && m.hasMore) {
                    await m.dataModule.updateTable({}, true);
                }
            },
            {
                root: wrapper,
                rootMargin: '100px',
                threshold: 0
            }
        );

        if (sentinel) observer.observe(sentinel);

        document.addEventListener('tableUpdated', () => {
            if (sentinel && sentinel.parentElement) {
                observer.unobserve(sentinel);
                sentinel.parentElement.removeChild(sentinel);
                sentinel = null;
            }

            if (m.hasMore) {
                sentinel = createSentinel();
                if (sentinel) observer.observe(sentinel);
            }
        });
    }
}
