import { route } from 'ziggy-js';
import { applyInputMasks } from '@js/components/ui/input-mask';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';
import { updateGenre } from '@js/api/genres/update';
import { initColorPicker } from './color-picker';

/**
 * Opens the update genre modal and binds form submission logic.
 * On success, it reopens the genre menu modal and refreshes the table.
 *
 * @param {string|number} genreId Genre ID to be updated.
 * @param {Object} modalManager Modal manager instance used to load and control modals.
 * @param {Object} refreshGenresList Function to refresh the genres list after update.
 * @returns {Promise<void>}
 */
export async function openUpdateModal(genreId, modalManager, refreshGenresList) {
    const url = route('genres.update-modal', { genre: genreId });

    try {
        await modalManager.loadModalContent(url, 'genreUpdateModal', {
            onInit: () => {
                applyInputMasks();
                initColorPicker();
            }
        });

        modalManager.bindFormSubmit({
            modalId: 'genreUpdateModal',
            buttonId: 'submit-update',
            onSubmit: (data) => updateGenre(genreId, data),
            onSuccess: () => {
                modalManager.removeModal('genreUpdateModal');
                refreshGenresList();
                notifySuccess('Gênero atualizado com sucesso!');
            },
            onError: (err) => {
                err.errors ? showErrors(err.errors) : notifyError(err.message || 'Erro ao atualizar o gênero');
            }
        });

    } catch (err) {
        console.error(err);
    }
}
