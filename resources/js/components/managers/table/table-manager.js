import '@css/components/table.css';
import TableLayout from './table-layout';
import TableData from './table-data';
import InfiniteScroll from './infinite-scroll';
import Sorting from './sorting';
import Events from './events';

/**
 * Table manager controller.
 */
export default class TableManager {
    /**
     * @param {Object} params
     * @param {Function} params.sendRequest Function that fetches table data.
     * @param {string} params.tableContainerId Table container element ID.
     * @param {string} [params.errorMessage] Message shown when request fails.
     * @param {string} [params.notFoundMessage] Message shown when no data is found.
     * @param {Function} params.renderRows Function that renders rows HTML.
     */
    constructor({ sendRequest, tableContainerId, errorMessage = 'Erro ao carregar dados.', notFoundMessage = 'Dados não encontrados.', renderRows }) {
        if (!sendRequest) throw new Error("sendRequest function is required");
        if (!renderRows) throw new Error("renderRows function is required");

        this.sendRequest = sendRequest;
        this.renderRows = renderRows;
        this.tableContainer = document.getElementById(tableContainerId);

        if (!this.tableContainer) {
            throw new Error(`Elemento com id ${tableContainerId} não encontrado`);
        }

        this.errorMessage = errorMessage;
        this.notFoundMessage = notFoundMessage;
        this.isLoading = false;
        this.hasMore = true;
        this.nextCursor = null;
        this.sortColumn = null;
        this.sortDirection = "asc";
        this.perPage = 15;
        this.currentParams = {};
        this.lastResponse = null;

        this.layoutModule = new TableLayout(this);
        this.dataModule = new TableData(this);
        this.scrollModule = new InfiniteScroll(this);
        this.sortingModule = new Sorting(this);
        this.eventsModule = new Events(this);

        requestAnimationFrame(() => {
            this.perPage = this.layoutModule.calculateVisibleRows();
            this.dataModule.loadInitialTable();
        });

        document.dispatchEvent(new CustomEvent('tableManagerReady', {
            detail: { instance: this }
        }));
    }

    /**
     * Updates the table data.
     *
     * @param {Object} [params]
     * @param {boolean} [append=false]
     * @returns {Promise<any>}
     */
    async updateTable(params = {}, append = false) {
        return this.dataModule.updateTable(params, append);
    }

    /**
     * Resets cursor pagination state.
     */
    resetPagination() {
        this.nextCursor = null;
        this.hasMore = true;
    }
}
