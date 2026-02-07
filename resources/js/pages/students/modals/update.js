import { route } from 'ziggy-js';
import { applyInputMasks } from '@js/components/ui/input-mask.js';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';
import { updateStudent } from '@js/api/students/update.js';
import { initStudentsSelects } from './selects';
import { openMenuModal } from './menu';

/**
 * Opens the "Update Student" modal and handles its behavior.
 *
 * @param {Object} modalManager - Modal controller instance
 * @param {number|string} studentId - ID of the student to update
 * @param {Object} studentsTable - Table instance for updating after changes
 */
export async function openUpdateModal(modalManager, studentId, studentsTable) {
    const url = route('students.update-modal', { student: studentId });

    try {
        await modalManager.loadModalContent(url, 'studentUpdateModal', {
            onInit: () => {
                applyInputMasks();
                if (window.App?.selectData) initStudentsSelects(window.App.selectData);
            }
        });

        document.getElementById('btn-cancel-update')?.addEventListener('click', () => {
            openMenuModal(modalManager, studentId, studentsTable);
        });

        modalManager.bindFormSubmit({
            modalId: 'studentUpdateModal',
            buttonId: 'submit-update',
            onSubmit: (data) => updateStudent(studentId, data),
            onSuccess: () => {
                openMenuModal(modalManager, studentId, studentsTable);
                studentsTable.updateTable();
                notifySuccess('Aluno atualizado com sucesso!');
            },
            onError: (err) => {
                err.errors ? showErrors(err.errors) : notifyError(err.message || 'Erro ao atualizar aluno');
            }
        });
    } catch (err) {
        console.error(err);
    }
}
