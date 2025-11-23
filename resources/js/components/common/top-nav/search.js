export const initSearch = (filterUIInstance) => {
    const searchInput = document.getElementById('top-nav-search-input');
    const updateTable = window.App?.updateTable;

    if (!searchInput || !filterUIInstance) return;

    let timeout = null;
    let lastRequestId = 0;

    searchInput.addEventListener('input', () => {
        clearTimeout(timeout);
        timeout = setTimeout(async () => {
            const searchTerm = searchInput.value.trim();
            const requestId = ++lastRequestId;
            if (updateTable) {
                try {
                    const newData = await updateTable({ search: searchTerm });
                    if (requestId === lastRequestId) filterUIInstance.updateData(newData.data?.filterData, true);
                } catch (err) {
                    console.error("Search update failed:", err);
                }
            }
        }, 300);
    });
};
