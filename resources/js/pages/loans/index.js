import { initLoansModals } from './loans-modals';
import { FilterUI } from '@js/components/common/filter/filter-ui/index';
import loansTable from '@js/components/managers/table/instances/loans-table';
import { setupTablePage } from '@js/components/setups/page-table-setup';
import { setupSidebarPage } from '@js/components/setups/page-sidebar-setup';

// Setup table page with filtering, table updates, and modals
setupTablePage({
    initFilterUI: () => {
        const filterUI = new FilterUI({
            filterData: window.App.filterData,
            onParamsChange: (params) => loansTable.updateTable(params),
            fields: [
                { key: 'genre_name', element: null, placeholder: 'Gênero' },
                { key: 'publisher', element: null, placeholder: 'Editora' },
                { key: 'course', element: null, placeholder: 'Curso' },
                { key: 'period', element: null, placeholder: 'Período', formatLabel: v => `${v}°` },
                { key: 'term', element: null, placeholder: 'Regime', formatLabel: v => v.toLowerCase() === 'annual' ? 'Anual' : 'Semestral' },
                { key: 'active', element: null, placeholder: 'Status', formatLabel: v => v === 1 ? 'Ativo' : 'Finalizado' },
            ]
        });

        filterUI.init();

        window.App.filterUIInstance = filterUI;

        return filterUI;
    },
    updateTable: (params) => loansTable.updateTable(params),
    filterStateKey: 'loansFilterState',
    filterData: window.App.filterData,
    initModals: modalManager => initLoansModals({ modalManager, table: loansTable })
});

setupSidebarPage();
