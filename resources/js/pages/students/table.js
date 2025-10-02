import { sendRequest } from '../../api/students/listFetcher';
import { bindPaginationForm } from '../../components/pagination';
import TableManager from '../../components/tableManager';

window.bindPaginationForm = bindPaginationForm;

const studentsTable = new TableManager({
    sendRequest,
    tableContainerId: 'data-table-container',
    paginationWrapperId: 'pagination-wrapper',
    errorMessage: 'Erro ao carregar alunos.'
});

export default studentsTable;
