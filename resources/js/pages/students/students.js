import { initStudentsModals } from './students-modals';
import { FilterUI } from '../../components/ui/filter-ui';
import studentsTable from './table';
import { setupTablePage } from '../../components/setups/page-table-setup';

setupTablePage({
    initFilterUI: () => {
        return new FilterUI({
            filterData: window.App.filterData,
            onParamsChange: (params) => studentsTable.updateTable(params),
            fields: [
                { key: 'course', element: null, placeholder: 'Curso' },
                { key: 'period', element: null, placeholder: 'Período', formatLabel: v => `${v}°` },
                { key: 'term', element: null, placeholder: 'Regime', formatLabel: v => v.toLowerCase() === 'annual' ? 'Anual' : 'Semestral' },
                { key: 'can_borrow', element: null, placeholder: 'Status', formatLabel: v => v === 1 ? 'Autorizado' : 'Bloqueado' },
            ]
        });
    },
    updateTable: (params) => studentsTable.updateTable(params),
    filterStateKey: 'studentsFilterState',
    filterData: window.App.filterData,
    initModals: initStudentsModals
});
