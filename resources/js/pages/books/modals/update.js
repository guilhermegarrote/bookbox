import { route } from 'ziggy-js';
import { applyInputMasks } from '@js/components/ui/input-mask';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';
import { updateBook } from '@js/api/books/update';
import { initBooksSelects } from './selects';
import { openMenuModal } from './menu';

/**
 * Opens the update book modal and binds form submission logic.
 * On success, it reopens the book menu modal and refreshes the table.
 *
 * @param {Object} modalManager Modal manager instance used to load and control modals.
 * @param {string|number} bookId Book ID to be updated.
 * @param {Object} booksTable Table instance used to refresh the list after update.
 * @returns {Promise<void>}
 */
export async function openUpdateModal(modalManager, bookId, booksTable) {
    const url = route('books.update-modal', { book: bookId });

    try {
        await modalManager.loadModalContent(url, 'bookUpdateModal', {
            onInit: () => {
                applyInputMasks();
                if (window.App?.selectData) initBooksSelects(window.App.selectData);
            }
        });

        document.getElementById('btn-cancel-update')?.addEventListener('click', () => {
            openMenuModal(modalManager, bookId, booksTable);
        });

        modalManager.bindFormSubmit({
            modalId: 'bookUpdateModal',
            buttonId: 'submit-update',
            onSubmit: (data) => updateBook(bookId, data),
            onSuccess: () => {
                openMenuModal(modalManager, bookId, booksTable);
                booksTable.updateTable();
                notifySuccess('Livro atualizado com sucesso!');
            },
            onError: (err) => {
                err.errors ? showErrors(err.errors) : notifyError(err.message || 'Erro ao atualizar o livro');
            }
        });

    } catch (err) {
        console.error(err);
    }
}

