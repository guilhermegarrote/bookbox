import { createStudent } from '../../api/students/create.js';
import { updateStudent } from '../../api/students/update.js';
import { deleteStudent } from '../../api/students/delete.js';
import studentsTable from '../../pages/students/table.js';
import { applyInputMasks } from '../../components/inputMask.js';
import ModalManager from '../../components/modalManager.js';
import { route } from 'ziggy-js';
import { showErrors, notifySuccess, notifyError } from '@/utils/formErrors';

const modalManager = new ModalManager();

async function openCreateModal() {
    const url = route('students.createModal');

    try {
        await modalManager.loadModalContent(url, 'studentCreateModal', {
            onInit: () => {
                applyInputMasks();
                if (window.App?.filterData) initStudentSelects(window.App.filterData);
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

async function openUpdateModal(studentId) {
    const url = route('students.updateModal', { student: studentId });

    try {
        await modalManager.loadModalContent(url, 'studentUpdateModal', {
            onInit: () => {
                applyInputMasks();
                if (window.App?.filterData) initStudentSelects(window.App.filterData);
            }
        });

        document.getElementById('btn-cancel-update')?.addEventListener('click', () => {
            openMenuModal(studentId);
        });

        modalManager.bindFormSubmit({
            modalId: 'studentUpdateModal',
            buttonId: 'submit-update',
            onSubmit: updateStudent,
            onSuccess: () => {
                openMenuModal(studentId);
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

async function openMenuModal(studentId) {
    const url = route('students.menuModal', { student: studentId });

    try {
        await modalManager.loadModalContent(url, 'studentMenuModal');

        document.getElementById('open-edit-modal')?.addEventListener('click', () => {
            openUpdateModal(studentId);
        });

        document.getElementById('submit-delete')?.addEventListener('click', async () => {
            const confirmed = await modalManager.showModalMessage({
                message: 'Deseja realmente excluir este aluno?',
                acceptText: 'Sim',
                declineText: 'Cancelar'
            });

            if (!confirmed) return;

            try {
                await deleteStudent(studentId);
                modalManager.removeModal('studentMenuModal');
                studentsTable.updateTable();
                notifySuccess('Aluno excluído com sucesso!');
            } catch (err) {
                notifyError(err.message || 'Erro ao excluir aluno');
            }
        });
    } catch (err) {
        console.error(err);
    }
}

export function initStudentSelects(filterData) {
    const courseSelect = document.getElementById('course');
    const periodSelect = document.getElementById('period');
    const termSelect = document.getElementById('term');
    if (!courseSelect || !periodSelect || !termSelect) return;

    const uniqueBy = (array, key) =>
        [...new Map(array.map(item => [item[key], item])).values()];

    const populateSelect = (select, items, valueKey, textKey, placeholder, presetValue) => {
        select.innerHTML = '';
        select.appendChild(new Option(placeholder, ''));
        items.forEach(item => select.appendChild(new Option(item[textKey], item[valueKey])));

        const initial = presetValue || select.dataset.value || '';
        if (initial && items.some(i => i[valueKey] == initial)) {
            select.value = initial;
        } else {
            select.selectedIndex = 0;
        }
        select.disabled = false;
    };

    function updateSelects(selected = {}) {
        const selectedCourse = selected.course || courseSelect.value || courseSelect.dataset.value || '';
        const selectedPeriod = selected.period || periodSelect.value || periodSelect.dataset.value || '';
        const selectedTerm = selected.term || termSelect.value || termSelect.dataset.value || '';

        const courses = uniqueBy(filterData, 'course').map(c => ({ value: c.course, label: c.course }));
        populateSelect(courseSelect, courses, 'value', 'label', 'Curso', selectedCourse);

        const filtered = selectedCourse ? filterData.filter(d => d.course === selectedCourse) : filterData;

        const periods = uniqueBy(filtered, 'period').map(p => ({ value: p.period, label: `${p.period}°` }));
        populateSelect(periodSelect, periods, 'value', 'label', 'Período', selectedPeriod);

        const terms = uniqueBy(filtered, 'term').map(t => ({
            value: t.term,
            label: t.term.toLowerCase() === 'annual' ? 'Anual' : 'Semestral'
        }));
        populateSelect(termSelect, terms, 'value', 'label', 'Regime', selectedTerm);
    }

    courseSelect.addEventListener('change', () => updateSelects({ course: courseSelect.value }));
    periodSelect.addEventListener('change', () => updateSelects({
        course: courseSelect.value,
        period: periodSelect.value
    }));
    termSelect.addEventListener('change', () => updateSelects({
        course: courseSelect.value,
        term: termSelect.value
    }));

    updateSelects();
}

function bindOpenButtons() {
    const addBtn = document.querySelector('.btn-add');
    if (addBtn) addBtn.addEventListener('click', openCreateModal);

    document.querySelectorAll('table.data-table tbody tr').forEach(row => {
        row.addEventListener('click', () => {
            const id = row.getAttribute('data-student-id');
            if (id) openMenuModal(id);
        });
    });
}

export function initStudentsModals() {
    bindOpenButtons();
    document.addEventListener('tableUpdated', bindOpenButtons);
}

export { openCreateModal, openUpdateModal, openMenuModal };
