async function openCreateModal() {
}

function bindOpenButtons() {
    const addBtn = document.querySelector('.btn-add');
    if (addBtn) addBtn.addEventListener('click', openCreateModal);

    document.querySelectorAll('table.data-table tbody tr').forEach(row => {
        row.addEventListener('click', () => {
            const id = row.getAttribute('data-book-id');
            if (id) openMenuModal(id);
        });
    });
}

export function initBooksModals() {
    bindOpenButtons();
    document.addEventListener('tableUpdated', bindOpenButtons);
}
