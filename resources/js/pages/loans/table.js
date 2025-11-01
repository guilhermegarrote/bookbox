import { sendRequest } from '../../api/loans/list-fetcher';
import { bindPaginationForm } from '../../components/ui/pagination';
import TableManager from '../../components/managers/table-manager';

window.bindPaginationForm = bindPaginationForm;

const loansTable = new TableManager({
    sendRequest,
    tableContainerId: 'data-table-container',
    paginationWrapperId: 'pagination-wrapper',
    errorMessage: 'Erro ao carregar empréstimos.'
});

export default loansTable;
