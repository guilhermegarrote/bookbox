import '../../../css/components/table.css';

export default class TableManager {
    constructor({ sendRequest, tableContainerId, paginationWrapperId, errorMessage = 'Erro ao carregar dados.' }) {
        if (!sendRequest) throw new Error("sendRequest function is required");

        this.sendRequest = sendRequest;
        this.tableContainer = document.getElementById(tableContainerId);
        if (!this.tableContainer) throw new Error(`Elemento com id ${tableContainerId} não encontrado`);

        this.paginationWrapper = document.getElementById(paginationWrapperId);
        this.errorMessage = errorMessage;

        this.currentParams = {};
        this.isLoading = false;

        this.initPagination();
        this.bindGlobalEvents();
    }

    async updateTable(params = {}) {
        if (this.isLoading) return [];

        this.isLoading = true;

        if ('search' in params) this.currentParams.page = 1;

        this.currentParams = { ...this.currentParams, ...params };

        try {
            const data = await this.sendRequest(this.currentParams);
            this.handleResponse(data);
            if (this.filterUIInstance && data.filterData) {
                this.filterUIInstance.updateData(data.filterData);
            }
            return data;
        } catch {
            this.handleError();
            return [];
        } finally {
            this.isLoading = false;
        }
    }

    handleResponse(data) {
        if (!data || !data.html) {
            this.tableContainer.innerHTML = `<p>${this.errorMessage}</p>`;
            if (this.paginationWrapper) this.paginationWrapper.innerHTML = '';
            return;
        }

        this.tableContainer.innerHTML = data.html;
        if (this.paginationWrapper) this.paginationWrapper.innerHTML = data.paginationHtml || '';
        this.bindPaginationForm();
        document.dispatchEvent(new Event('tableUpdated'));
    }

    handleError() {
        this.tableContainer.innerHTML = `<p>${this.errorMessage}</p>`;
        if (this.paginationWrapper) this.paginationWrapper.innerHTML = '';
    }

    bindPaginationForm() {
        if (typeof this.bindPaginationFormCallback === 'function') {
            this.bindPaginationFormCallback();
        }
    }

    bindGlobalEvents() {
        this.tableContainer.addEventListener('click', (e) => {
            const pageBtn = e.target.closest('.pagination-form button.page-link');
            if (pageBtn) {
                e.preventDefault();
                if (pageBtn.disabled) return;
                const page = pageBtn.getAttribute('data-page') || 1;
                const perPage = pageBtn.getAttribute('data-per-page') || this.currentParams.perPage || 15;
                this.updateTable({ page, perPage });
            }

            const sortLink = e.target.closest('.sort-link');
            if (sortLink) {
                e.preventDefault();
                const url = new URL(sortLink.href);
                const params = Object.fromEntries(url.searchParams.entries());
                if (this.currentParams.perPage && !params.perPage) {
                    params.perPage = this.currentParams.perPage;
                }
                this.updateTable({ ...this.currentParams, ...params });
            }
        });

        document.addEventListener('filtersUpdated', (e) => {
            const params = e.detail || {};
            this.updateTable(params);
        });
    }

    initPagination() {
        const initValues = () => {
            const perPageSelect = document.querySelector('#paginationForm #perPage');
            this.currentParams.perPage = perPageSelect?.value || 15;
            this.bindPaginationForm();
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initValues);
        } else {
            initValues();
        }
    }
}
