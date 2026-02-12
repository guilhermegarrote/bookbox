import { route } from 'ziggy-js';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';
import { createSchoolClass } from '@js/api/school-classes/create';
import { applyInputMasks } from '@js/components/ui/input-mask';

/**
 * Opens the "Create School Class" modal and handles its behavior.
 *
 * @param {Object} modalManager - Modal controller instance
 * @param {Object} refreshSchoolClassesList - Function to refresh the school classes list after creation
 */
export async function openCreateModal(modalManager, refreshSchoolClassesList = '') {
    const url = route('school-classes.create-modal');

    try {
        await modalManager.loadModalContent(url, 'schoolClassCreateModal', {
            onInit: () => {
                setDefaultDates();
                applyInputMasks();
            }
        });

        modalManager.bindFormSubmit({
            modalId: 'schoolClassCreateModal',
            buttonId: 'submit-create',
            onSubmit: createSchoolClass,
            onSuccess: () => {
                modalManager.removeModal('schoolClassCreateModal');
                if (refreshSchoolClassesList) refreshSchoolClassesList();
                notifySuccess('Turma cadastrada com sucesso!');
            },
            onError: (err) => {
                err.errors ? showErrors(err.errors) : notifyError(err || 'Erro ao cadastrar turma');
            }
        });
    } catch (err) {
        console.error(err);
    }
}

/**
 * Sets fixed default dates:
 * - start_date: Feb 1 of current year
 * - end_date: Dec 1 of current year + 2
 */
function setDefaultDates() {
    const year = new Date().getFullYear();

    [
        { id: 'start_date', date: new Date(year, 1, 1) },   // Feb 1
        { id: 'end_date', date: new Date(year + 2, 11, 1) } // Dec 1
    ].forEach(({ id, date }) => {
        const input = document.getElementById(id);
        if (!input) return;

        const yyyy = date.getFullYear();
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');

        input.value = `${dd}/${mm}/${yyyy}`;
        input.classList.add('has-value');
    });
}

