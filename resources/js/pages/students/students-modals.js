import { createStudent } from '../../api/students/create.js';
import { updateTable } from '../../pages/students/table.js';
import { applyInputMasks } from '../../components/inputMask.js';
import ModalManager from '../../components/modalManager.js';
import { route } from 'ziggy-js';

const modalManager = new ModalManager();

async function openCreateModal() {
    const url = route('students.createModal');
    try {
        await modalManager.loadModalContent(url, 'studentCreateModal');

        applyInputMasks();

        if (window.filterData) {
            initStudentSelects(window.filterData);
        } else {
            console.warn('filterData não encontrado para popular selects.');
        }

        const submitBtn = document.getElementById('submit-create');
        if (submitBtn) {
            submitBtn.addEventListener('click', async () => {
                const modal = document.getElementById('studentCreateModal');

                const data = {
                    name: modal.querySelector('#name')?.value?.trim(),
                    term: modal.querySelector('#term')?.value?.trim(), // Corrigido: 'term' no lugar de duplicar 'period'
                    course: modal.querySelector('#course')?.value?.trim(),
                    phone: modal.querySelector('#phone')?.value?.trim(),
                    cpf: modal.querySelector('#cpf')?.value?.trim(),
                    email: modal.querySelector('#email')?.value?.trim(),
                };

                try {
                    const result = await createStudent(data);
                    console.log('Aluno cadastrado:', result);

                    modalManager.hideModal('studentCreateModal');

                    // Atualiza a tabela após o cadastro
                    updateTable();

                } catch (err) {
                    console.error('Erro ao cadastrar aluno:', err.message);

                    // Aqui você pode exibir erros de validação, se quiser
                    // exibirErrosNoModal(err);
                }
            });
        }

    } catch (err) {
        console.error('Erro ao abrir modal de cadastro:', err);
    }
}

async function openMenuModal(studentId) {
    const url = route('students.menuModal', { student: studentId });
    try {
        await modalManager.loadModalContent(url, 'studentMenuModal');
    } catch (err) {
        console.error('Erro ao abrir modal de menu:', err);
    }
}

function bindOpenButtons() {
    const addBtn = document.querySelector('.btn-add');
    if (addBtn) {
        addBtn.addEventListener('click', openCreateModal);
    }

    document.querySelectorAll('table.data-table tbody tr').forEach(row => {
        row.addEventListener('click', () => {
            const id = row.getAttribute('data-student-id');
            if (id) openMenuModal(id);
        });
    });
}

export function initStudentSelects(filterData) {
    const courseSelect = document.getElementById('student-course');
    const periodSelect = document.getElementById('student-period');
    const termSelect = document.getElementById('student-term');

    if (!courseSelect || !periodSelect || !termSelect) {
        console.warn('Selects do modal não encontrados no DOM.');
        return;
    }

    function uniqueBy(array, key) {
        return [...new Map(array.map(item => [item[key], item])).values()];
    }

    function populateSelect(select, items, valueKey, textKey, placeholderText) {
        const currentValue = select.value;
        select.innerHTML = '';

        const placeholderOption = new Option(placeholderText, '');
        select.appendChild(placeholderOption);

        items.forEach(item => {
            const option = new Option(item[textKey], item[valueKey]);
            select.appendChild(option);
        });

        if (currentValue === '') {
            select.selectedIndex = 0;
        } else if (items.some(i => i[valueKey] === currentValue)) {
            select.value = currentValue;
        } else {
            select.selectedIndex = 0;
        }

        select.disabled = false;
    }

    function updateSelects(changedSelect) {
        const selectedCourse = courseSelect.value;
        const selectedPeriod = periodSelect.value;
        const selectedTerm = termSelect.value;

        let filtered = filterData;
        if (selectedCourse) filtered = filtered.filter(d => d.course === selectedCourse);
        if (selectedPeriod) filtered = filtered.filter(d => d.period === selectedPeriod);
        if (selectedTerm) filtered = filtered.filter(d => d.term === selectedTerm);

        if (!selectedCourse) {
            const allPeriods = uniqueBy(filterData, 'period')
                .map(p => ({ value: String(p.period), label: `${p.period}°` }));
            populateSelect(periodSelect, allPeriods, 'value', 'label', 'Período');

            const allTerms = uniqueBy(filterData, 'term')
                .map(t => ({
                    value: t.term,
                    label: t.term.toLowerCase() === 'annual' ? 'Anual' : 'Semestral'
                }));
            populateSelect(termSelect, allTerms, 'value', 'label', 'Regime');
        }

        if (changedSelect !== courseSelect) {
            const courses = uniqueBy(filterData, 'course').map(c => ({ value: c.course, label: c.course }));
            populateSelect(courseSelect, courses, 'value', 'label', 'Curso');
        }

        if (changedSelect !== periodSelect) {
            const periods = uniqueBy(
                selectedCourse ? filterData.filter(d => d.course === selectedCourse) : filterData,
                'period'
            ).map(p => ({ value: p.period, label: `${p.period}°` }));
            populateSelect(periodSelect, periods, 'value', 'label', 'Período');
        }

        if (changedSelect !== termSelect) {
            const terms = uniqueBy(
                selectedCourse ? filterData.filter(d => d.course === selectedCourse) : filterData,
                'term'
            ).map(t => ({
                value: t.term,
                label: t.term.toLowerCase() === 'annual' ? 'Anual' : 'Semestral'
            }));
            populateSelect(termSelect, terms, 'value', 'label', 'Regime');
        }
    }

    courseSelect.addEventListener('change', () => updateSelects(courseSelect));
    periodSelect.addEventListener('change', () => updateSelects(periodSelect));
    termSelect.addEventListener('change', () => updateSelects(termSelect));

    updateSelects();
}

function initStudentsModals() {
    bindOpenButtons();
}

export { initStudentsModals, openCreateModal, openMenuModal };
