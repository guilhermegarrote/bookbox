import { initBooksModals } from './books-modals';
import { FilterUI } from '../../components/filterUI';
import booksTable from './table';
import { setupTablePage } from '../../components/pageTableSetup';

setupTablePage({
    initFilterUI: () => {
        return new FilterUI({
            filterData: window.App.filterData,
            onParamsChange: (params) => booksTable.updateTable(params),
            fields: [
                { key: 'genre_name', element: null, placeholder: 'Gênero' },
                { key: 'publisher', element: null, placeholder: 'Editora' },
            ]
        });
    },
    updateTable: (params) => booksTable.updateTable(params),
    filterStateKey: 'booksFilterState',
    filterData: window.App.filterData,
    initModals: initBooksModals
});
