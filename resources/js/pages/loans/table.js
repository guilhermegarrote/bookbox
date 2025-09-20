import { sendRequest } from '../../api/loans/listFetcher';
import { bindPaginationForm } from '../../components/pagination';
import TableManager from '../../components/tableManager';

window.bindPaginationForm = bindPaginationForm;

const loansTable = new TableManager({
    sendRequest,
    tableContainerId: 'data-table-container',
    paginationWrapperId: 'pagination-wrapper',
    errorMessage: 'Erro ao carregar empréstimos.'
});

export default loansTable;
