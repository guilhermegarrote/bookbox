import { route } from 'ziggy-js';
import { applyInputMasks } from '@js/components/ui/input-mask.js';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';
import { updateStudent } from '@js/api/students/update.js';
import { initStudentsSelects } from './selects';
import { openMenuModal } from './menu';
import { openCreateModal as openSchoolClassCreateModal } from '@js/pages/school-classes/modals/create';

let _modalManagerRef = null;
let _studentsTableRef = null;
let _reopenListenerAttached = false;

const modalId = 'studentUpdateModal';

/**
 * Opens the "Update Student" modal and handles its behavior.
 *
 * @param {Object} modalManager - Modal controller instance
 * @param {number|string} studentId - ID of the student to update
 * @param {Object} studentsTable - Table instance for updating after changes
 */
export async function openUpdateModal(modalManager, studentId, studentsTable, initialData = null) {
    const url = route('students.update-modal', { student: studentId });

    try {
        _modalManagerRef = modalManager;
        _studentsTableRef = studentsTable;

        if (!_reopenListenerAttached) {
            window.addEventListener('reopenStudentModal', (e) => {
                const saved = e.detail;
                const data = saved?.data ?? null;

                if (_modalManagerRef && _studentsTableRef) {
                    openUpdateModal(_modalManagerRef, studentId, _studentsTableRef, data);
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

        document.getElementById('open-create-school-class-modal')?.addEventListener('click', () => {
            modalManager.saveModalState('student:update:pending', modalId);
            openSchoolClassCreateModal(modalManager);
        });

        document.getElementById('btn-close')?.addEventListener('click', () => {
            openMenuModal(modalManager, studentId, studentsTable);
            modalManager.removeModal(modalId);
        });

        modalManager.bindFormSubmit({
            modalId,
            buttonId: 'submit-update',
            onSubmit: (data) => updateStudent(studentId, data),
            onSuccess: () => {
                openMenuModal(modalManager, studentId, studentsTable);
                studentsTable.updateTable();
                notifySuccess('Aluno atualizado com sucesso!');
            },
            onError: (err) => {
                err.errors ? showErrors(err.errors) : notifyError(err || 'Erro ao atualizar aluno');
            }
        });
    } catch (err) {
        console.error(err);
    }
}
