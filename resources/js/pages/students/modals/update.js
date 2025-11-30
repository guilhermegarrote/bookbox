import { route } from 'ziggy-js';
import { applyInputMasks } from '@js/components/ui/input-mask.js';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';
import { updateStudent } from '@js/api/students/update.js';
import { initStudentsSelects } from './selects';
import { openMenuModal } from './menu';

export async function openUpdateModal(modalManager, studentId, studentsTable) {
    const url = route('students.update-modal', { student: studentId });

    try {
        await modalManager.loadModalContent(url, 'studentUpdateModal', {
            onInit: () => {
                applyInputMasks();
                if (window.App?.filterData) initStudentsSelects(window.App.filterData);
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
