/**
 * Initializes the Students modals and related handlers for the page.
 *
 * @param {Object} options
 * @param {Object} options.modalManager - Modal controller instance
 * @param {Object} options.table - Students table instance to update after actions
 */
export function initStudentsModals({ modalManager, table }) {
    const handlers = {
        openMenuModal: (id) => openMenuModal(modalManager, id, table),
        openCreateModal: () => openCreateModal(modalManager, table),
    };

    bindOpenButtons(handlers);

    initBarcodeScannerListener(handlers.openMenuModal);

    document.addEventListener('tableUpdated', () => {
        bindOpenButtons(handlers);
    });
}

export {
    openCreateModal,
    openUpdateModal,
    openMenuModal,
    initStudentsSelects
};
