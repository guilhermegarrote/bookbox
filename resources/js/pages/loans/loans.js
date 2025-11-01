import { initLoansModals } from './loans-modals';
import { FilterUI } from '../../components/ui/filter-ui';
import loansTable from './table';
import { setupTablePage } from '../../components/setups/page-table-setup';
import { setupSidebarPage } from '../../components/setups/page-sidebar-setup';

setupTablePage({
    initFilterUI: () => {
        return new FilterUI({
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
    },
    updateTable: (params) => loansTable.updateTable(params),
    filterStateKey: 'loansFilterState',
    filterData: window.App.filterData,
    initModals: initLoansModals
});

setupSidebarPage();
