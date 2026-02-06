/**
 * Initializes the search input debounce and updates the filter UI data.
 *
 * @param {Object} filterUIInstance Filter UI instance.
 * @param {Function} filterUIInstance.updateData Updates filter UI data.
 */
export const initSearch = (filterUIInstance) => {
    const searchInput = document.getElementById('top-nav-search-input');

    const updateTable = window.App?.updateTable;

    if (!searchInput || !filterUIInstance) return;

    let timeout = null;

    let lastRequestId = 0;

    searchInput.addEventListener('input', () => {
        if (timeout) clearTimeout(timeout);

        timeout = setTimeout(async () => {
            const searchTerm = searchInput.value.trim();
            const requestId = ++lastRequestId;

            if (!updateTable) return;

            try {
                const newData = await updateTable({ search: searchTerm });

                if (requestId === lastRequestId) {
                    filterUIInstance.updateData(newData.data?.filterData, true);
                }
            } catch (err) {
                console.error("Search update failed:", err);
            }
        }, 300);
    });
};
