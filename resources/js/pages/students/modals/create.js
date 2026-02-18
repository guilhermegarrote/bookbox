import { route } from 'ziggy-js';
import { applyInputMasks } from '@js/components/ui/input-mask';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';
import { createStudent } from '@js/api/students/create';
import { initStudentsSelects } from './selects';
import { openCreateModal as openSchoolClassCreateModal } from '@js/pages/school-classes/modals/create';

let _modalManagerRef = null;
let _studentsTableRef = null;
let _reopenListenerAttached = false;

const modalId = 'studentCreateModal';

/**
 * Opens the "Create Student" modal and handles its behavior.
 *
 * @param {Object} modalManager - Modal controller instance
 * @param {Object} studentsTable - Table instance for updating after creation
 */
export async function openCreateModal(modalManager, studentsTable, initialData = null) {
    const url = route('students.create-modal');

    try {
        _modalManagerRef = modalManager;
        _studentsTableRef = studentsTable;

        if (!_reopenListenerAttached) {
            window.addEventListener('reopenStudentModal', (e) => {
                const saved = e.detail;
                const data = saved?.data ?? null;

                if (_modalManagerRef && _studentsTableRef) {
                    openCreateModal(_modalManagerRef, _studentsTableRef, data);
                }
            });

            _reopenListenerAttached = true;
        }

        await modalManager.loadModalContent(url, modalId, {
            onInit: () => {
                applyInputMasks();

                const studentModal = document.getElementById(modalId);

                if (studentModal) {
                    const selectData = JSON.parse(studentModal.dataset.select || '[]');

                    initStudentsSelects(selectData);
                }
            },
            initialData
        });

        document.getElementById('open-create-school-class-modal')?.addEventListener('click', async () => {
            modalManager.saveModalState('student:create:pending', modalId);
            openSchoolClassCreateModal(modalManager);
        });

        modalManager.bindFormSubmit({
            modalId,
            buttonId: 'submit-create',
            onSubmit: createStudent,
            onSuccess: () => {
                modalManager.dispatchSavedModalEvent('loan:create:pending', 'reopenLoanModal');

                modalManager.removeModal(modalId);
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
