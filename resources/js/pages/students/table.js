import { sendRequest } from '../../api/students/list-fetcher';
import { bindPaginationForm } from '../../components/ui/pagination';
import TableManager from '../../components/managers/table-manager';

window.bindPaginationForm = bindPaginationForm;

const studentsTable = new TableManager({
    sendRequest,
    tableContainerId: 'data-table-container',
    paginationWrapperId: 'pagination-wrapper',
    errorMessage: 'Erro ao carregar alunos.'
});

export default studentsTable;
