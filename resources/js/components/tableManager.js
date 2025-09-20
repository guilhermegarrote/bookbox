import '../../css/components/table.css';

export default class TableManager {
    constructor({ sendRequest, tableContainerId, paginationWrapperId, errorMessage }) {
        if (!sendRequest) throw new Error("sendRequest function is required");

        this.sendRequest = sendRequest;
        this.tableContainer = document.getElementById(tableContainerId);
        this.paginationWrapper = document.getElementById(paginationWrapperId);
        this.errorMessage = errorMessage || 'Erro ao carregar dados.';

        this.currentParams = {};
        this.isLoading = false;

        this.bindGlobalEvents();
        this.initPagination();
    }

    async updateTable(params = {}) {
        if (this.isLoading) return [];

        this.isLoading = true;

        if (params.hasOwnProperty('search')) {
            this.currentParams.page = 1;
        }

        this.currentParams = { ...params };

        try {
            const data = await this.sendRequest(this.currentParams);
            this.handleResponse(data);

            if (window.filterUIInstance && data.filterData) {
                window.filterUIInstance.updateData(data.filterData);
            }

            return data;
        } catch (err) {
            this.handleError(err);
            return [];
        } finally {
            this.isLoading = false;
        }
    }

    handleResponse(data) {
        if (data.html) {
            this.tableContainer.innerHTML = data.html;

            if (data.paginationHtml && this.paginationWrapper) {
                this.paginationWrapper.innerHTML = data.paginationHtml;
            }

            this.bindPaginationForm();
        } else {
            this.tableContainer.innerHTML = `<p>${this.errorMessage}</p>`;
        }
    }

    handleError(error) {
        console.error('Erro ao carregar dados:', error);
        this.tableContainer.innerHTML = `<p>${this.errorMessage}</p>`;
    }

    bindPaginationForm() {
        if (window.bindPaginationForm) {
            window.bindPaginationForm();
        }
    }

    bindGlobalEvents() {
        document.addEventListener('click', (e) => {
            const target = e.target;

            if (target.matches('.pagination-form button.page-link')) {
                e.preventDefault();
                if (target.disabled) return;

                const page = target.getAttribute('data-page') || 1;
                const perPage = target.getAttribute('data-per-page') || this.currentParams.perPage || 15;

                this.updateTable({ page, perPage });
            }

            let sortTarget = e.target;
            while (sortTarget && sortTarget !== document.body && !sortTarget.classList.contains('sort-link')) {
                sortTarget = sortTarget.parentElement;
            }

            if (sortTarget && sortTarget.classList.contains('sort-link')) {
                e.preventDefault();
                const url = new URL(sortTarget.href);
                const params = Object.fromEntries(url.searchParams.entries());

                if (this.currentParams.perPage && !params.perPage) {
                    params.perPage = this.currentParams.perPage;
                }

                this.updateTable(params);
            }
        });

        document.addEventListener('filtersUpdated', (e) => {
            const params = e.detail || {};
            this.updateTable(params);
        });
    }

    initPagination() {
        document.addEventListener('DOMContentLoaded', () => {
            const perPageSelect = document.querySelector('#paginationForm #perPage');
            this.currentParams.perPage = perPageSelect ? perPageSelect.value : 15;

            this.bindPaginationForm();
        });
    }
}
