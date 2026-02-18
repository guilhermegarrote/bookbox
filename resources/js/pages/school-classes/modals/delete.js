import { deleteSchoolClass as deleteSchoolClassRequest } from '@js/api/school-classes/delete';
import { notifySuccess, notifyError } from '@js/utils/formErrors';

/**
 * Handles school class deletion with confirmation modal.
 *
 * @param {string|number} schoolClassId School class ID to be deleted.
 * @param {Object} modalManager Modal manager instance used to show confirmation.
 * @param {Object} refreshSchoolClassesList Function to refresh the school classes list after deletion.
 * @returns {Promise<void>}
 */
export async function handleDeleteSchoolClass(schoolClassId, modalManager, refreshSchoolClassesList) {
    const confirmed = await modalManager.showModalMessage({
        message: "Deseja realmente excluir esta turma?",
        acceptText: "Sim",
        declineText: "Cancelar"
    });

    if (!confirmed) return;

    try {
        const { ok, data: responseData } = await deleteSchoolClassRequest(schoolClassId);

        if (!ok) {
            notifyError(responseData || 'Erro desconhecido.');
            return;
        }

        refreshSchoolClassesList();
        notifySuccess('Turma excluída com sucesso!');
    } catch (error) {
        console.error(error);
        notifyError('Erro técnico ao tentar excluir a turma. Tente novamente.');
    }
}
