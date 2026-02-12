import { route } from 'ziggy-js';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';
import { updateSchoolClass } from '@js/api/school-classes/update';
import { applyInputMasks } from '@js/components/ui/input-mask';

/**
 * Opens the update school class modal and binds form submission logic.
 * On success, it reopens the school class menu modal and refreshes the table.
 *
 * @param {string|number} schoolClassId School Class ID to be updated.
 * @param {Object} modalManager Modal manager instance used to load and control modals.
 * @param {Object} refreshSchoolClassesList Function to refresh the school classes list after update.
 * @returns {Promise<void>}
 */
export async function openUpdateModal(schoolClassId, modalManager, refreshSchoolClassesList) {
    const url = route('school-classes.update-modal', { schoolClass: schoolClassId });

    try {
        await modalManager.loadModalContent(url, 'schoolClassUpdateModal', {
            onInit: () => {
                applyInputMasks();
            }
        });

        modalManager.bindFormSubmit({
            modalId: 'schoolClassUpdateModal',
            buttonId: 'submit-update',
            onSubmit: (data) => updateSchoolClass(schoolClassId, data),
            onSuccess: () => {
                modalManager.removeModal('schoolClassUpdateModal');
                refreshSchoolClassesList();
                notifySuccess('Turma atualizada com sucesso!');
            },
            onError: (err) => {
                err.errors ? showErrors(err.errors) : notifyError(err || 'Erro ao atualizar a turma');
            }
        });

    } catch (err) {
        console.error(err);
    }
}
