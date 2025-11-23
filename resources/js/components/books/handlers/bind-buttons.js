
export function bindOpenButtons({
    openMenuModal,
    openCreateModal,
    openGeneraLabelModal
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

            openGeneraLabelModal();
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
