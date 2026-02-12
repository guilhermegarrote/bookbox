import { route } from 'ziggy-js';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';
import { createUser } from '@js/api/users/create';

/**
 * Opens the "Create User" modal and handles form submission.
 *
 * @param {Object} modalManager - Instance controlling modals
 * @param {Object} refreshSchoolClassesList - Function to refresh the school classes list after user creation
 */
export async function openCreateModal(modalManager, refreshSchoolClassesList = '') {
    const url = route('users.create-modal');

    try {
        await modalManager.loadModalContent(url, 'userCreateModal');

        modalManager.bindFormSubmit({
            modalId: 'userCreateModal',
            buttonId: 'submit-create',
            onSubmit: createUser,
            onSuccess: () => {
                modalManager.removeModal('userCreateModal');
                if (refreshSchoolClassesList) refreshSchoolClassesList();
                notifySuccess('Usuário cadastrado com sucesso!');
            },
            onError: (err) => {
                err.errors ? showErrors(err.errors) : notifyError(err || 'Erro ao cadastrar usuário');
            }
        });
    } catch (err) {
        console.error(err);
    }
}
