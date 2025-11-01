import { sendRequest } from '../../api/books/list-fetcher';
import { bindPaginationForm } from '../../components/ui/pagination';
import TableManager from '../../components/managers/table-manager';

window.bindPaginationForm = bindPaginationForm;

const booksTable = new TableManager({
    sendRequest,
    tableContainerId: 'data-table-container',
    paginationWrapperId: 'pagination-wrapper',
    errorMessage: 'Erro ao carregar livros.'
});

export default booksTable;
