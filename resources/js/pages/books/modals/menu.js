import { route } from 'ziggy-js';
import { deleteBook } from '@js/api/books/delete.js';
import { notifySuccess, notifyError } from '@js/utils/formErrors';
import { openUpdateModal } from './update';
import { openGenerateLabelModal } from '../../labels/labels-modals.js';
import { openManagerCopiesModal } from '../../copies/copies-modals.js';

export async function openMenuModal(modalManager, bookId, booksTable) {
    const url = route('books.menuModal', { book: bookId });

    try {
        await modalManager.loadModalContent(url, 'bookMenuModal');

        document.getElementById('open-edit-modal')?.addEventListener('click', () => {
            openUpdateModal(modalManager, bookId, booksTable);
        });

        document.getElementById('open-manager-copies-modal')?.addEventListener('click', () => {
            openManagerCopiesModal(bookId, modalManager);
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
                await deleteBook(bookId);
                modalManager.removeModal('bookMenuModal');
                booksTable.updateTable();
                notifySuccess('Livro excluído com sucesso!');
            } catch (err) {
                notifyError(err.message || 'Erro ao excluir o livro');
            }
        });

    } catch (err) {
        console.error(err);
    }
}
