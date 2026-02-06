import { initStudentsModals } from './students-modals';
import { FilterUI } from '@js/components/common/filter/filter-ui/index';
import studentsTable from '@js/components/managers/table/instances/students-table';
import { setupTablePage } from '@js/components/setups/page-table-setup';

setupTablePage({
    initFilterUI: () => {
        const filterUI = new FilterUI({
            filterData: window.App.filterData,
            onParamsChange: (params) => studentsTable.updateTable(params),
            fields: [
                { key: 'course', element: null, placeholder: 'Curso' },
                { key: 'period', element: null, placeholder: 'Período', formatLabel: v => `${v}°` },
                { key: 'term', element: null, placeholder: 'Regime', formatLabel: v => v.toLowerCase() === 'annual' ? 'Anual' : 'Semestral' },
                { key: 'can_borrow', element: null, placeholder: 'Status', formatLabel: v => v === 1 ? 'Autorizado' : 'Bloqueado' },
            ]
        });

        filterUI.init();

        window.App.filterUIInstance = filterUI;

        return filterUI;
    },
    updateTable: (params) => studentsTable.updateTable(params),
    filterStateKey: 'studentsFilterState',
    filterData: window.App.filterData,
    selectData: window.App.selectData,
    initModals: modalManager => initStudentsModals({ modalManager, table: studentsTable })
});
