/**
 * Binds click events for opening modals.
 *
 * @param {Object} params
 * @param {Function} params.openMenuModal Opens the menu modal.
 * @param {Function} params.openCreateModal Opens the create modal.
 */
export function bindOpenButtons({
    openMenuModal,
    openCreateModal
}) {
    const addBtn = document.querySelector('.btn-add');
    if (addBtn && !addBtn.dataset.bound) {
        addBtn.dataset.bound = "1";
        addBtn.addEventListener('click', () =>
            openCreateModal()
        );
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
