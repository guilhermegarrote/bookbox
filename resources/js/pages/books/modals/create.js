import { route } from 'ziggy-js';
import { applyInputMasks } from '@js/components/ui/input-mask.js';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';
import { createBook } from '@js/api/books/create.js';
import { initBooksSelects } from './selects';
import { initIsbnAutoFill } from './isbn-autofill.js';

export async function openCreateModal(modalManager, booksTable) {
    const url = route('books.createModal');
    await loadBookModal(url, modalManager, booksTable);
}

export async function openCreateModalWithIsbn(modalManager, booksTable, isbn) {
    const url = route('books.createModal');
    await loadBookModal(url, modalManager, booksTable, isbn);
}

async function loadBookModal(url, modalManager, booksTable, isbn = null) {
    try {
        await modalManager.loadModalContent(url, 'bookCreateModal', {
            onInit: () => {
                applyInputMasks();
                if (window.App?.filterData) initBooksSelects(window.App.filterData);
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
                modalManager.removeModal('bookCreateModal');
                booksTable.updateTable();
                notifySuccess('Livro cadastrado com sucesso!');
            },
            onError: (err) => {
                err.errors ? showErrors(err.errors) : notifyError(err.message || 'Erro ao cadastrar livro');
            }
        });
    } catch (err) {
        console.error(err);
    }
}

