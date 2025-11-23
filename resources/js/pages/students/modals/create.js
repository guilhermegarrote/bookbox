import { route } from 'ziggy-js';
import { applyInputMasks } from '@js/components/ui/input-mask.js';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';
import { createStudent } from '@js/api/students/create.js';
import { initStudentsSelects } from './selects';

export async function openCreateModal(modalManager, studentsTable) {
    const url = route('students.createModal');

    try {
        await modalManager.loadModalContent(url, 'studentCreateModal', {
            onInit: () => {
                applyInputMasks();
                if (window.App?.filterData) initStudentsSelects(window.App.filterData);
            }
        });

        modalManager.bindFormSubmit({
            modalId: 'studentCreateModal',
            buttonId: 'submit-create',
            onSubmit: createStudent,
            onSuccess: () => {
                modalManager.removeModal('studentCreateModal');
                studentsTable.updateTable();
                notifySuccess('Aluno cadastrado com sucesso!');
            },
            onError: (err) => {
                err.errors ? showErrors(err.errors) : notifyError(err.message || 'Erro ao cadastrar aluno');
            }
        });
    } catch (err) {
        console.error(err);
    }
}
