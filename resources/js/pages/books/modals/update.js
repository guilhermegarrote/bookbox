import { route } from 'ziggy-js';
import { applyInputMasks } from '@js/components/ui/input-mask';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';
import { updateBook } from '@js/api/books/update';
import { initBooksSelects } from './selects';
import { openMenuModal } from './menu';
import { openCreateModal as openGenreCreateModal } from '@js/pages/genres/modals/create';

let _modalManagerRef = null;
let _booksTableRef = null;
let _reopenListenerAttached = false;

const modalId = 'bookUpdateModal';

/**
 * Opens the update book modal and binds form submission logic.
 * On success, it reopens the book menu modal and refreshes the table.
 *
 * @param {Object} modalManager Modal manager instance used to load and control modals.
 * @param {string|number} bookId Book ID to be updated.
 * @param {Object} booksTable Table instance used to refresh the list after update.
 * @returns {Promise<void>}
 */
export async function openUpdateModal(modalManager, bookId, booksTable, initialData = null) {
    const url = route('books.update-modal', { book: bookId });

    try {
        _modalManagerRef = modalManager;
        _booksTableRef = booksTable;

        if (!_reopenListenerAttached) {
            window.addEventListener('reopenBookModal', (e) => {
                const saved = e.detail;
                const data = saved?.data ?? null;

                if (_modalManagerRef && _booksTableRef) {
                    openUpdateModal(_modalManagerRef, bookId, _booksTableRef, data);
                }
            });

            _reopenListenerAttached = true;
        }

        await modalManager.loadModalContent(url, modalId, {
            onInit: () => {
                applyInputMasks();

                const bookModal = document.getElementById(modalId);

                if (bookModal) {
                    const selectData = JSON.parse(bookModal.dataset.select || '[]');

                    initBooksSelects(selectData);
                }
            },
            initialData
        });

        document.getElementById('open-create-genre-modal')?.addEventListener('click', async () => {
            modalManager.saveModalState('book:update:pending', modalId);
            openGenreCreateModal(modalManager);
        });

        document.getElementById('btn-close')?.addEventListener('click', () => {
            openMenuModal(modalManager, bookId, booksTable);
        });

        modalManager.bindFormSubmit({
            modalId,
            buttonId: 'submit-update',
            onSubmit: (data) => updateBook(bookId, data),
            onSuccess: () => {
                openMenuModal(modalManager, bookId, booksTable);
                booksTable.updateTable();
                notifySuccess('Livro atualizado com sucesso!');
            },
            onError: (err) => {
                err.errors ? showErrors(err.errors) : notifyError(err || 'Erro ao atualizar o livro');
            }
        });

    } catch (err) {
        console.error(err);
    }
}

