import { route } from 'ziggy-js';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';
import { updateUser } from '@js/api/users/update';

const modalId = 'userUpdateModal';

/**
 * Opens the "Update User" modal and handles form submission.
 *
 * @param {string|number} userId User ID to be updated.
 * @param {Object} modalManager Modal manager instance used to load and control modals.
 * @param {Object} refreshUsersList Function to refresh the users list after update.
 * @returns {Promise<void>}
 */
export async function openUpdateModal(userId, modalManager, refreshUsersList) {
    const url = route('users.update-modal', { user: userId });

    try {
        await modalManager.loadModalContent(url, modalId);

        modalManager.bindFormSubmit({
            modalId,
            buttonId: 'submit-update',
            onSubmit: (data) => updateUser(userId, data),
            onSuccess: () => {
                modalManager.removeModal(modalId);
                refreshUsersList();
                notifySuccess('Usuário atualizado com sucesso!');
            },
            onError: (err) => {
                err.errors ? showErrors(err.errors) : notifyError(err || 'Erro ao atualizar usuário');
            }
        });
    } catch (err) {
        console.error(err);
    }
}
