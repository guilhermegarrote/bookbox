import { route } from 'ziggy-js';
import { applyInputMasks } from '@js/components/ui/input-mask';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';
import { createStudent } from '@js/api/students/create';
import { initStudentsSelects } from './selects';

/**
 * Opens the "Create Student" modal and handles its behavior.
 *
 * @param {Object} modalManager - Modal controller instance
 * @param {Object} studentsTable - Table instance for updating after creation
 */
export async function openCreateModal(modalManager, studentsTable) {
    const url = route('students.create-modal');

    try {
        await modalManager.loadModalContent(url, 'studentCreateModal', {
            onInit: () => {
                applyInputMasks();
                if (window.App?.selectData) initStudentsSelects(window.App.selectData);
            }
        });

        modalManager.bindFormSubmit({
            modalId: 'studentCreateModal',
            buttonId: 'submit-create',
            onSubmit: createStudent,
            onSuccess: () => {
                modalManager.dispatchSavedModalEvent('loan:create:pending', 'reopenLoanModal');

                modalManager.removeModal('studentCreateModal');
                studentsTable.updateTable();
                notifySuccess('Aluno cadastrado com sucesso!');
            },
            onError: (err) => {
                err.errors ? showErrors(err.errors) : notifyError(err || 'Erro ao cadastrar aluno');
            }
        });
    } catch (err) {
        console.error(err);
    }
}
