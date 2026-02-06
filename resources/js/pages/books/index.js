import { initBooksModals } from './books-modals';
import { FilterUI } from '@js/components/common/filter/filter-ui';
import booksTable from '@js/components/managers/table/instances/books-table';
import { setupTablePage } from '@js/components/setups/page-table-setup';

setupTablePage({
    initFilterUI: () => {
        const filterUI = new FilterUI({
            filterData: window.App.filterData,
            onParamsChange: params => booksTable.updateTable(params),
            fields: [
                { key: 'genre_name', element: null, placeholder: 'Gênero' },
                { key: 'publisher', element: null, placeholder: 'Editora' },
            ]
        });

        filterUI.init();

        window.App.filterUIInstance = filterUI;

        return filterUI;
    },
    updateTable: params => booksTable.updateTable(params),
    filterStateKey: 'booksFilterState',
    filterData: window.App.filterData,
    selectData: window.App.selectData,
    initModals: modalManager => initBooksModals({ modalManager, table: booksTable })
});
