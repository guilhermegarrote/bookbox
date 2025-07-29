import { sendRequest } from '../../api/students/listFetcher';
import { bindPaginationForm } from '../../components/pagination';

let currentParams = {};
let isLoading = false;

function updateTable(params = {}) {
    if (isLoading) return;

    isLoading = true;

    if (params.hasOwnProperty('search')) {
        currentParams.page = 1;
    }

    currentParams = { ...params };

    sendRequest(currentParams)
        .then(data => handleResponse(data))
        .catch(handleError)
        .finally(() => {
            isLoading = false;
        });
}

function handleResponse(data) {
    const tableContainer = document.getElementById('data-table-container');
    const paginationWrapper = document.getElementById('pagination-wrapper');

    if (data.html) {
        tableContainer.innerHTML = data.html;

        if (data.paginationHtml && paginationWrapper) {
            paginationWrapper.innerHTML = data.paginationHtml;
        }

        bindPaginationForm();
    } else {
        tableContainer.innerHTML = '<p>Erro ao carregar alunos.</p>';
    }
}

function handleError(error) {
    console.error('Erro ao carregar dados:', error);
}

document.addEventListener('click', (e) => {
    const target = e.target;

    if (target.matches('.pagination-form button.page-link')) {
        e.preventDefault();
        if (target.disabled) return;

        const page = target.getAttribute('data-page') || 1;
        const perPage = target.getAttribute('data-per-page') || currentParams.perPage || 15;

        updateTable({ page, perPage });
    }

    document.addEventListener('click', (e) => {
        let target = e.target;

        while (target && target !== document.body && !target.classList.contains('sort-link')) {
            target = target.parentElement;
        }

        if (target && target.classList.contains('sort-link')) {
            e.preventDefault();
            const url = new URL(target.href);
            const params = Object.fromEntries(url.searchParams.entries());

            if (currentParams.perPage && !params.perPage) {
                params.perPage = currentParams.perPage;
            }

            updateTable(params);
        }
    });
});

document.addEventListener('filtersUpdated', (e) => {
    const params = e.detail || {};
    updateTable(params);
});

document.addEventListener('DOMContentLoaded', () => {
    const perPageSelect = document.querySelector('#paginationForm #perPage');
    currentParams.perPage = perPageSelect ? perPageSelect.value : 15;

    bindPaginationForm();
});


export { updateTable };
