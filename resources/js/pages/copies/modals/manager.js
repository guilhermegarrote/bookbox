import { notifySuccess, notifyError } from '@js/utils/formErrors';
import { addCopies } from '@js/api/copies/add.js';
import { deleteCopy } from '@js/api/copies/delete.js';
import { openMenuModal } from '@js/pages/books/modals/menu.js';

export async function openManagerCopiesModal(modalManager, bookId, booksTable) {
    const url = route('copies.manager-modal', { book: bookId });

    try {
        await modalManager.loadModalContent(url, 'copyManagerModal');

        const modal = document.querySelector('#copyManagerModal');
        const closeButton = clearCloseButtonListener(modal);

        closeButton.addEventListener('click', () => openMenuModal(modalManager, bookId, booksTable));

        document.getElementById('open-add-modal')?.addEventListener('click', () => {
            openAddModal(modalManager, bookId, booksTable);
        });

        document.querySelectorAll('.btn-trash').forEach(button => {
            button.addEventListener('click', async () => {
                const copyId = button.dataset.id;

                const confirmed = await modalManager.showModalMessage({
                    message: 'Deseja realmente excluir este exemplar?',
                    acceptText: 'Sim',
                    declineText: 'Cancelar'
                });

                if (!confirmed) return;

                try {
                    await deleteCopy(copyId);
                    openManagerCopiesModal(bookId, modalManager);
                    notifySuccess('Exemplar excluído com sucesso!');
                } catch (err) {
                    notifyError(err.message || 'Erro ao excluir o exemplar');
                }
            });
        });

    } catch (error) {
        console.error(error);
    }
}

async function openAddModal(modalManager, bookId, booksTable) {
    const url = route('copies.add-modal');

    try {
        await modalManager.loadModalContent(url, 'copyAddModal');

        const modal = document.querySelector('#copyAddModal');
        const closeButton = clearCloseButtonListener(modal);

        closeButton.addEventListener('click', () => openManagerCopiesModal(modalManager, bookId, booksTable));

        const decrementButton = document.getElementById('decrement');
        const incrementButton = document.getElementById('increment');
        const quantityInput = document.getElementById('amount');

        decrementButton.addEventListener('click', () => {
            const value = parseInt(quantityInput.value, 10);
            if (value > 1) quantityInput.value = value - 1;
        });

        incrementButton.addEventListener('click', () => {
            const value = parseInt(quantityInput.value, 10);
            if (value < 32766) {
                quantityInput.value = value + 1;
            } else {
                quantityInput.value = 32766;
            }
        });

        quantityInput.addEventListener('input', () => {
            const value = parseInt(quantityInput.value, 10);
            if (value < 1 || isNaN(value)) quantityInput.value = 1;
            if (value > 32766) quantityInput.value = 32766;
        });

        modalManager.bindFormSubmit({
            modalId: 'copyAddModal',
            buttonId: 'submit-create',
            onSubmit: data => addCopies(bookId, data),
            onSuccess: () => {
                modalManager.removeModal('copyAddModal');
                booksTable.updateTable();
                notifySuccess('Exemplares cadastrados com sucesso!');
                openManagerCopiesModal(modalManager, bookId, booksTable);
            },
            onError: error => {
                error.errors ? showErrors(error.errors) : notifyError(error.message || 'Error registering copies');
            }
        });
    } catch (error) {
        console.error(error);
    }
}

function clearCloseButtonListener(modal) {
    const closeButton = modal.querySelector('#btn-close');
    const newCloseButton = closeButton.cloneNode(true);
    closeButton.parentNode.replaceChild(newCloseButton, closeButton);
    return newCloseButton;
}
