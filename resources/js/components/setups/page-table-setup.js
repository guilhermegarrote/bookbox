import ModalManager from '@js/components/ui/modal-manager.js';

export function setupTablePage({ initFilterUI, updateTable, filterStateKey, filterData, initModals }) {
    window.App = {
        initFilterUI,
        updateTable,
        filterStateKey,
        filterData: filterData || {}
    };

    document.addEventListener('DOMContentLoaded', () => {
        resizeTableWrapper();

        if (initModals) {
            const modalManager = new ModalManager();
            initModals(modalManager);
        }
    });

    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => resizeTableWrapper(), 200);
    });
}

function getTotalVerticalSpace(element) {
    if (!element) return 0;
    const style = getComputedStyle(element);
    return (
        element.offsetHeight +
        (parseFloat(style.marginTop) || 0) +
        (parseFloat(style.marginBottom) || 0) +
        (parseFloat(style.paddingTop) || 0) +
        (parseFloat(style.paddingBottom) || 0)
    );
}

function resizeTableWrapper() {
    const windowHeight = window.innerHeight;
    const header = document.querySelector('header');
    const title = document.querySelector('.page-title');
    const tableWrapper = document.querySelector('.table-wrapper');
    const panel = document.querySelector('.panel');
    if (!tableWrapper) return;

    const headerSpace = getTotalVerticalSpace(header);
    const titleSpace = getTotalVerticalSpace(title);
    const extraSpacing = panel ? parseFloat(getComputedStyle(panel).marginRight) || 0 : 0;

    const availableHeight = windowHeight - headerSpace - titleSpace - extraSpacing;
    tableWrapper.style.height = `${availableHeight}px`;

    const visibleRows = calculateVisibleRows(tableWrapper);

    if (window.tableManagerInstance) {
        window.tableManagerInstance.perPage = visibleRows;
    } else if (window.booksTable) {
        window.booksTable.perPage = visibleRows;
    }
}

function calculateVisibleRows() {
    const tableWrapper = document.querySelector('.table-wrapper');
    if (!tableWrapper) return 15;

    const firstRow = tableWrapper.querySelector('tbody tr');
    if (!firstRow) return 15;

    const wrapperHeight = tableWrapper.clientHeight;
    const rowHeight = firstRow.offsetHeight || 36;
    const visibleRows = Math.floor(wrapperHeight / rowHeight);

    return visibleRows > 0 ? visibleRows : 15;
}

