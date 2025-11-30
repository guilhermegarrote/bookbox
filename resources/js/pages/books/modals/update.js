import { route } from 'ziggy-js';
import { applyInputMasks } from '@js/components/ui/input-mask.js';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';
import { updateBook } from '@js/api/books/update.js';
import { initBooksSelects } from './selects';
import { openMenuModal } from './menu';

export async function openUpdateModal(modalManager, bookId, booksTable) {
    const url = route('books.update-modal', { book: bookId });

    try {
        await modalManager.loadModalContent(url, 'bookUpdateModal', {
            onInit: () => {
                applyInputMasks();
                if (window.App?.filterData) initBooksSelects(window.App.filterData);
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
