export default class TableData {
    constructor(manager) {
        this.manager = manager;
    }

    async loadInitialTable() {
        await this.updateTable({}, false);
    }

    async updateTable(params = {}, append = false) {
        const m = this.manager;
        if (m.isLoading || (!m.hasMore && append)) return;

        m.isLoading = true;
        const loader = document.getElementById('loader');
        if (loader) loader.style.display = 'block';

        m.currentParams = { ...m.currentParams, ...params };

        try {
            const queryParams = {
                ...m.currentParams,
                perPage: m.perPage,
                sort: m.sortColumn,
                direction: m.sortDirection,
            };

            if (m.nextCursor && append) queryParams.cursor = m.nextCursor;

            const data = await m.sendRequest(queryParams);
            this.handleResponse(data, append);

            return data;
        } catch (err) {
            console.error('TableData updateTable error:', err);
            this.handleError();
            return [];
        } finally {
            m.isLoading = false;
            if (loader) loader.style.display = 'none';
        }
    }

    handleResponse(data, append = false) {
        const m = this.manager;

        m.lastResponse = data;

        const rowsData = data?.data?.data ?? [];
        const tableWrapper = document.getElementById('data-table-container');
        const tbody = m.tableContainer.querySelector('tbody');
        const loader = document.getElementById('loader');

        const existingMessage = tableWrapper.querySelector('.data-empty');
        if (existingMessage) existingMessage.remove();
        const sentinel = tableWrapper.querySelector('#infinite-scroll-sentinel');
        if (sentinel) sentinel.remove();

        if (!rowsData.length) {
            if (!append) {
                tbody.innerHTML = '';
                const msgDiv = document.createElement('div');
                msgDiv.className = 'data-empty';
                msgDiv.innerHTML = `<h1>${m.notFoundMessage}</h1>`;
                tableWrapper.appendChild(msgDiv);
            }
            m.hasMore = false;
            if (loader) loader.style.display = 'none';
            return;
        }

        const newRows = m.renderRows(rowsData);
        if (append) tbody.insertAdjacentHTML('beforeend', newRows);
        else tbody.innerHTML = newRows;

        m.nextCursor = data.data.pagination?.next_cursor || null;
        m.hasMore = data.data.pagination?.has_more ?? false;

        if (m.hasMore) {
            const newSentinel = document.createElement('div');
            newSentinel.id = 'infinite-scroll-sentinel';
            newSentinel.style.height = '30px';
            newSentinel.style.width = '100%';
            tableWrapper.appendChild(newSentinel);
        }

        const filterData = data.data.filterData;
        if (filterData && window.filterUIInstance) {
            window.filterUIInstance.updateData(filterData, false);
        }

        if (loader) loader.style.display = 'none';
        m.sortingModule.updateSortIcons();
        document.dispatchEvent(new Event('tableUpdated'));
    }

    handleError() {
        const m = this.manager;
        const tbody = m.tableContainer.querySelector('tbody');
        const tableWrapper = document.getElementById('data-table-container');

        tbody.innerHTML = '';
        const existingMessage = tableWrapper.querySelector('.data-empty');
        if (existingMessage) existingMessage.remove();

        const sentinel = tableWrapper.querySelector('#infinite-scroll-sentinel');
        if (sentinel) sentinel.remove();

        const msgDiv = document.createElement('div');
        msgDiv.className = 'data-empty';
        msgDiv.innerHTML = `<h1>${m.errorMessage}</h1>`;
        tableWrapper.appendChild(msgDiv);
    }
}
