import { route } from 'ziggy-js';
import { notifySuccess, notifyError } from '@js/utils/formErrors';
import { deleteUser } from '@js/api/users/delete';

/**
 * Opens the "Delete User" modal and handles form submission.
 *
 * @param {string|number} userId User ID to be deleted.
 * @param {Object} modalManager Modal manager instance used to load and control modals.
 * @param {Object} refreshUsersList Function to refresh the users list after update.
 * @returns {Promise<void>}
 */
export async function openDeleteModal(userId, modalManager, refreshUsersList) {
    const url = route('users.delete-modal');

    try {
        await modalManager.loadModalContent(url, 'userDeleteModal');

        const submitBtn = document.getElementById('submit-delete');

        if (!submitBtn) return;

        submitBtn.onclick = async () => {
            const data = modalManager.getModalData('userDeleteModal');

            const confirmed = await modalManager.showModalMessage({
                message: 'Deseja realmente excluir este usuário?',
                acceptText: 'Sim',
                declineText: 'Cancelar'
            });

            if (!confirmed) return;

            try {
                const { ok, data: responseData } = await deleteUser(userId, data);

                if (!ok) {
                    notifyError(responseData || 'Erro desconhecido.');
                } else {
                    modalManager.removeModal('userDeleteModal');
                    refreshUsersList();
                    notifySuccess('Usuário excluído com sucesso!');
                }
            } catch (error) {
                console.error(error);
                notifyError('Erro técnico ao tentar excluir o usuário. Tente novamente.');
            }
        };
    } catch (err) {
        console.error(err);
    }
}
