import '@css/components/table.css';
import TableLayout from './table-layout.js';
import TableData from './table-data.js';
import InfiniteScroll from './infinite-scroll.js';
import Sorting from './sorting.js';
import Events from './events.js';

export default class TableManager {
    constructor({ sendRequest, tableContainerId, errorMessage = 'Erro ao carregar dados.', notFoundMessage = 'Dados não encontrados.', renderRows }) {
        if (!sendRequest) throw new Error("sendRequest function is required");
        if (!renderRows) throw new Error("renderRows function is required");

        this.sendRequest = sendRequest;
        this.renderRows = renderRows;
        this.tableContainer = document.getElementById(tableContainerId);
        if (!this.tableContainer) throw new Error(`Elemento com id ${tableContainerId} não encontrado`);

        this.errorMessage = errorMessage;
        this.notFoundMessage = notFoundMessage;
        this.isLoading = false;
        this.hasMore = true;
        this.nextCursor = null;
        this.sortColumn = null;
        this.sortDirection = "asc";

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

        this.lastResponse = null;
    }

    async updateTable(params = {}, append = false) {
        return this.dataModule.updateTable(params, append);
    }

    resetPagination() {
        this.nextCursor = null;
        this.hasMore = true;
    }
}
