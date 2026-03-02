import { notifySuccess, notifyError } from '@js/utils/formErrors';
import { addCopies } from '@js/api/copies/add';
import { deleteCopy } from '@js/api/copies/delete';
import { openMenuModal } from '@js/pages/books/modals/menu';

/**
 * Opens the modal for managing book copies.
 * Allows deleting copies and opening the add copies modal.
 *
 * @param {Object} modalManager Modal manager instance used to control modals.
 * @param {string|number} bookId Book ID used to load and manage copies.
 * @param {Object} booksTable Table instance used to refresh the books list.
 * @returns {Promise<void>}
 */
export async function openManagerCopiesModal(modalManager, bookId, booksTable) {
    const url = route('copies.manager-modal', { book: bookId });
    const modalId = 'copyManagerModal';

    try {
        await modalManager.loadModalContent(url, modalId);

        document.getElementById('btn-close')?.addEventListener('click', () => {
            openMenuModal(modalManager, bookId, booksTable);
        });

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

                if (!confirmed) {
                    openManagerCopiesModal(modalManager, bookId, booksTable);
                    return;
                }

                try {
                    await deleteCopy(copyId);
                    booksTable.updateTable();
                    openManagerCopiesModal(modalManager, bookId, booksTable);
                    notifySuccess('Exemplar excluído com sucesso!');
                } catch (err) {
                    notifyError(err || 'Erro ao excluir o exemplar');
                }
            });
        });

    } catch (error) {
        console.error(error);
    }
}

/**
 * Opens the modal for adding multiple copies of a book.
 * Provides increment/decrement controls and validates the quantity input.
 *
 * @param {Object} modalManager Modal manager instance used to control modals.
 * @param {string|number} bookId Book ID used when creating new copies.
 * @param {Object} booksTable Table instance used to refresh the books list.
 * @returns {Promise<void>}
 */
async function openAddModal(modalManager, bookId, booksTable) {
    const url = route('copies.add-modal');
    const modalId = 'copyAddModal';

    try {
        await modalManager.loadModalContent(url, modalId);

        document.getElementById('btn-close')?.addEventListener('click', () => {
            openManagerCopiesModal(modalManager, bookId, booksTable);
        });

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
            modalId,
            buttonId: 'submit-create',
            onSubmit: data => addCopies(bookId, data),
            onSuccess: () => {
                openManagerCopiesModal(modalManager, bookId, booksTable);
                booksTable.updateTable();
                notifySuccess('Exemplares cadastrados com sucesso!');
            },
            onError: error => {
                error.errors ? showErrors(error.errors) : notifyError(error.message || 'Erro ao cadastrar os exemplares');
            }
        });
    } catch (error) {
        console.error(error);
    }
}
