import { deleteGenre as deleteGenreRequest } from '@js/api/genres/delete';
import { notifySuccess, notifyError } from '@js/utils/formErrors';

/**
 * Handles genre deletion with confirmation modal.
 *
 * @param {string|number} genreId Genre ID to be deleted.
 * @param {Object} modalManager Modal manager instance used to show confirmation.
 * @param {Object} refreshGenresList Function to refresh the genres list after deletion.
 * @returns {Promise<void>}
 */
export async function handleDeleteGenre(genreId, modalManager, refreshGenresList) {
    const confirmed = await modalManager.showModalMessage({
        message: "Deseja realmente excluir este gênero?",
        acceptText: "Sim",
        declineText: "Cancelar"
    });

    if (!confirmed) return;

    try {
        const { ok, data: responseData } = await deleteGenreRequest(genreId);

        if (!ok) {
            notifyError(responseData?.error || 'Erro desconhecido.');
            return;
        }

        refreshGenresList();
        notifySuccess('Gênero excluído com sucesso!');
    } catch (error) {
        console.error(error);
        notifyError('Erro técnico ao tentar excluir o gênero. Tente novamente.');
    }
}
