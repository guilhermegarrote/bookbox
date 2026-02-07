import { route } from 'ziggy-js';
import { deleteBook } from '@js/api/books/delete';
import { notifySuccess, notifyError } from '@js/utils/formErrors';
import { openUpdateModal } from './update';
import { openGenerateLabelModal } from '../../labels/labels-modals';
import { openManagerCopiesModal } from '../../copies/modals/manager';

/**
 * Opens the book menu modal and binds its action buttons (edit, manage copies,
 * generate labels, and delete).
 *
 * @param {Object} modalManager
 * @param {number|string} bookId
 * @param {Object} booksTable
 */
export async function openMenuModal(modalManager, bookId, booksTable) {
    const url = route('books.menu-modal', { book: bookId });

    try {
        await modalManager.loadModalContent(url, 'bookMenuModal');

        document.getElementById('open-edit-modal')?.addEventListener('click', () => {
            openUpdateModal(modalManager, bookId, booksTable);
        });

        document.getElementById('open-manager-copies-modal')?.addEventListener('click', () => {
            openManagerCopiesModal(modalManager, bookId, booksTable);
        });

        document.getElementById('open-generate-label-modal')?.addEventListener('click', () => {
            openGenerateLabelModal(modalManager);
        });

        document.getElementById('submit-delete')?.addEventListener('click', async () => {
            const confirmed = await modalManager.showModalMessage({
                message: 'Deseja realmente excluir este livro?',
                acceptText: 'Sim',
                declineText: 'Cancelar'
            });

            if (!confirmed) return;

            try {
                const { ok, data: responseData } = await deleteBook(bookId);

                if (!ok) {
                    notifyError(responseData?.error || 'Erro desconhecido.');
                } else {
                    modalManager.removeModal('bookMenuModal');
                    booksTable.updateTable();
                    notifySuccess('Livro excluído com sucesso!');
                }
            } catch (error) {
                console.error(error);
                notifyError('Erro técnico ao tentar excluir o livro. Tente novamente.');
            }
        });
    } catch (err) {
        console.error(err);
    }
}
