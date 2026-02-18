import { route } from 'ziggy-js';
import { applyInputMasks } from '@js/components/ui/input-mask';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';
import { createBook } from '@js/api/books/create';
import { initBooksSelects } from './selects';
import { initIsbnAutoFill } from './isbn-autofill';

const modalId = 'bookCreateModal';

/**
 * Opens the book creation modal.
 *
 * @param {Object} modalManager
 * @param {Object} booksTable
 */
export async function openCreateModal(modalManager, booksTable) {
    await loadBookModal(modalManager, booksTable);
}

/**
 * Opens the book creation modal and pre-fills ISBN.
 *
 * @param {Object} modalManager
 * @param {Object} booksTable
 * @param {string} isbn
 */
export async function openCreateModalWithIsbn(modalManager, booksTable, isbn) {
    await loadBookModal(modalManager, booksTable, isbn);
}

/**
 * Loads the modal content and binds submit events.
 *
 * @param {string} url
 * @param {Object} modalManager
 * @param {Object} booksTable
 * @param {string|null} isbn
 */
async function loadBookModal(modalManager, booksTable, isbn = null) {
    const url = route('books.create-modal');

    try {
        await modalManager.loadModalContent(url, modalId, {
            onInit: () => {
                applyInputMasks();

                const bookModal = document.getElementById(modalId);

                if (bookModal) {
                    const selectData = JSON.parse(bookModal.dataset.select || '[]');

                    initBooksSelects(selectData);
                }

                initIsbnAutoFill();

                if (isbn) {
                    const field = document.getElementById('isbn');
                    if (field) {
                        field.value = isbn;
                        field.dispatchEvent(new Event('input'));
                        field.dispatchEvent(new Event('keyup'));
                    }
                }
            }
        });

        modalManager.bindFormSubmit({
            modalId,
            buttonId: 'submit-create',
            onSubmit: createBook,
            onSuccess: () => {
                modalManager.dispatchSavedModalEvent('loan:create:pending', 'reopenLoanModal');

                modalManager.removeModal(modalId);
                booksTable.updateTable();
                notifySuccess('Livro cadastrado com sucesso!');
            },
            onError: (err) => {
                if (err.errors) {
                    showErrors(err.errors);
                } else {
                    notifyError(err || 'Erro ao cadastrar livro');
                }
            }
        });
    } catch (err) {
        console.error(err);
    }
}
