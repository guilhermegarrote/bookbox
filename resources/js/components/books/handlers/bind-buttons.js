/**
 * Binds click handlers to UI elements responsible for opening modals.
 *
 * @param {Object} params
 * @param {Function} params.openMenuModal
 *        Callback invoked with the row ID when a table row is clicked.
 * @param {Function} params.openCreateModal
 *        Callback invoked when the "add" button is clicked.
 * @param {Function} params.openGenerateLabelModal
 *        Callback invoked when the "label" button is clicked.
 *
 * @returns {void}
 */
export function bindOpenButtons({
    openMenuModal,
    openCreateModal,
    openGenerateLabelModal
}) {
    const addBtn = document.querySelector('.btn-add');
    if (addBtn && !addBtn.dataset.bound) {
        addBtn.dataset.bound = "1";
        addBtn.addEventListener('click', () =>
            openCreateModal()
        );
    }

    const labelBtn = document.querySelector('.btn-label');
    if (labelBtn && !labelBtn.dataset.bound) {
        labelBtn.dataset.bound = "1";

        labelBtn.addEventListener('click', () => {
            const modal = document.querySelector('.modal.show');
            if (modal) return;

            openGenerateLabelModal();
        });
    }

    const tableElem = document.querySelector('table.data-table tbody');
    if (tableElem && !tableElem.dataset.bound) {
        tableElem.dataset.bound = "1";

        tableElem.addEventListener('click', e => {
            const row = e.target.closest('tr[data-id]');
            if (!row) return;

            const id = row.dataset.id;
            openMenuModal(id);
        });
    }
}
