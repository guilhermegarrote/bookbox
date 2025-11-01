import { addCopies } from '../../api/copies/add.js';

export async function openManagerCopiesModal(bookId, modalManager) {
    const url = route('copies.managerModal', { book: bookId });

    try {
        await modalManager.loadModalContent(url, 'copyManagerModal');

        document.getElementById('open-add-modal')?.addEventListener('click', () => {
            openAddModal(bookId, modalManager);
        });
    } catch (err) {
        console.error(err);
    }
}

async function openAddModal(bookId, modalManager) {
    const url = route('copies.addModal');

    try {
        await modalManager.loadModalContent(url, 'copyAddModal', {});

        modalManager.bindFormSubmit({
            modalId: 'copyAddModal',
            buttonId: 'submit-add',
            onSubmit: addCopies(bookId, data),
            onSuccess: () => {
                modalManager.openManagerCopiesModal(bookId)
                booksTable.updateTable();
                notifySuccess('Exemplares cadastrados com sucesso!');
            },
            onError: (err) => {
                err.errors ? showErrors(err.errors) : notifyError(err.message || 'Erro ao cadastrar exemplares');
            }
        });
    } catch (err) {
        console.error(err);
    }
}
