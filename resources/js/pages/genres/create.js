import { route } from 'ziggy-js';
import { applyInputMasks } from '@js/components/ui/input-mask';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';
import { createGenre } from '@js/api/genres/create';
import { initColorPicker } from './color-picker';

/**
 * Opens the "Create Genre" modal and handles its behavior.
 *
 * @param {Object} modalManager - Modal controller instance
 * @param {Object} refreshGenresList - Function to refresh the genres list after creation
 */
export async function openCreateModal(modalManager, refreshGenresList = '') {
    const url = route('genres.create-modal');

    try {
        await modalManager.loadModalContent(url, 'genreCreateModal', {
            onInit: () => {
                applyInputMasks();
                initColorPicker();
            }
        });

        modalManager.bindFormSubmit({
            modalId: 'genreCreateModal',
            buttonId: 'submit-create',
            onSubmit: createGenre,
            onSuccess: () => {
                modalManager.removeModal('genreCreateModal');
                if (refreshGenresList) refreshGenresList();
                notifySuccess('Gênero cadastrado com sucesso!');
            },
            onError: (err) => {
                err.errors ? showErrors(err.errors) : notifyError(err.message || 'Erro ao cadastrar gênero');
            }
        });
    } catch (err) {
        console.error(err);
    }
}
