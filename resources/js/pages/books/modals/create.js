import { route } from 'ziggy-js';
import { applyInputMasks } from '@js/components/ui/input-mask.js';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';
import { createBook } from '@js/api/books/create.js';
import { initBooksSelects } from './selects';
import { initIsbnAutoFill } from './isbn-autofill.js';

/**
 * Opens the book creation modal.
 *
 * @param {Object} modalManager
 * @param {Object} booksTable
 */
export async function openCreateModal(modalManager, booksTable) {
    const url = route('books.create-modal');
    await loadBookModal(url, modalManager, booksTable);
}

/**
 * Opens the book creation modal and pre-fills ISBN.
 *
 * @param {Object} modalManager
 * @param {Object} booksTable
 * @param {string} isbn
 */
export async function openCreateModalWithIsbn(modalManager, booksTable, isbn) {
    const url = route('books.create-modal');
    await loadBookModal(url, modalManager, booksTable, isbn);
}

/**
 * Loads the modal content and binds submit events.
 *
 * @param {string} url
 * @param {Object} modalManager
 * @param {Object} booksTable
 * @param {string|null} isbn
 */
async function loadBookModal(url, modalManager, booksTable, isbn = null) {
    try {
        await modalManager.loadModalContent(url, 'bookCreateModal', {
            onInit: () => {
                applyInputMasks();

                if (window.App?.selectData) {
                    initBooksSelects(window.App.selectData);
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
            modalId: 'bookCreateModal',
            buttonId: 'submit-create',
            onSubmit: createBook,
            onSuccess: () => {
                modalManager.dispatchSavedModalEvent('loan:create:pending', 'reopenLoanModal');

                modalManager.removeModal('bookCreateModal');
                booksTable.updateTable();
                notifySuccess('Livro cadastrado com sucesso!');
            },
            onError: (err) => {
                if (err.errors) {
                    showErrors(err.errors);
                } else {
                    notifyError(err.message || 'Erro ao cadastrar livro');
                }
            }
        });
    } catch (err) {
        console.error(err);
    }
}
