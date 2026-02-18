import ModalManager from '@js/components/ui/modal-manager/modal-manager';

/**
 * Initializes the table page and global App config.
 *
 * @param {Object} params
 * @param {Function} params.initFilterUI Initializes filter UI instance.
 * @param {Function} params.updateTable Updates table data.
 * @param {string} params.filterStateKey Key used to store filter state.
 * @param {Object} [params.filterData] Initial filter data.
 * @param {Function} [params.initModals] Initializes modals using ModalManager.
 */
export function setupTablePage({ initFilterUI, updateTable, filterStateKey, filterData, initModals }) {
    window.App = {
        initFilterUI,
        updateTable,
        filterStateKey,
        filterData: filterData || {}
    };

    document.addEventListener('DOMContentLoaded', () => {
        if (initModals) {
            const modalManager = new ModalManager();
            initModals(modalManager);
        }
    });
}
