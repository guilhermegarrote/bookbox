import { sendRequest } from '../../api/books/listFetcher';
import { bindPaginationForm } from '../../components/pagination';
import TableManager from '../../components/tableManager';

window.bindPaginationForm = bindPaginationForm;

const booksTable = new TableManager({
    sendRequest,
    tableContainerId: 'data-table-container',
    paginationWrapperId: 'pagination-wrapper',
    errorMessage: 'Erro ao carregar livros.'
});

export default booksTable;
