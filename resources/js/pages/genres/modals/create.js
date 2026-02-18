import { route } from 'ziggy-js';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';
import { createGenre } from '@js/api/genres/create';
import { initColorPicker } from './color-picker';

const modalId = 'genreCreateModal';

/**
 * Opens the "Create Genre" modal and handles its behavior.
 *
 * @param {Object} modalManager - Modal controller instance
 * @param {Object} refreshGenresList - Function to refresh the genres list after creation
 */
export async function openCreateModal(modalManager, refreshGenresList = '') {
    const url = route('genres.create-modal');

    try {
        await modalManager.loadModalContent(url, modalId, {
            onInit: () => {
                initColorPicker();
            }
        });

        document.getElementById('btn-close')?.addEventListener('click', () => {
            modalManager.dispatchSavedModalEvent('book:create:pending', 'reopenBookModal');
            modalManager.dispatchSavedModalEvent('book:update:pending', 'reopenBookModal');
            modalManager.removeModal(modalId);
        });

        modalManager.bindFormSubmit({
            modalId,
            buttonId: 'submit-create',
            onSubmit: createGenre,
            onSuccess: () => {
                modalManager.dispatchSavedModalEvent('book:create:pending', 'reopenBookModal');
                modalManager.dispatchSavedModalEvent('book:update:pending', 'reopenBookModal');

                modalManager.removeModal(modalId);
                if (refreshGenresList) refreshGenresList();
                notifySuccess('Gênero cadastrado com sucesso!');
            },
            onError: (err) => {
                err.errors ? showErrors(err.errors) : notifyError(err || 'Erro ao cadastrar gênero');
            }
        });
    } catch (err) {
        console.error(err);
    }
}
