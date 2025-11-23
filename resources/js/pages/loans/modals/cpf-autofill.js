import { findStudentByCpf } from '@js/api/students/find-by-cpf.js';
import { notifyError } from '@/utils/formErrors';

export function initCpfAutoFill() {
    const cpfInput = document.getElementById('cpf');
    if (!cpfInput) return;

    let timeout = null;
    let lastCpf = null;
    let lastResult = null;

    cpfInput.addEventListener('keyup', () => {
        clearTimeout(timeout);

        timeout = setTimeout(async () => {
            const cpf = cpfInput.value.replace(/\D/g, '');
            if (cpf.length < 11) return;

            await autofillFromCpf(cpf);
        }, 300);
    });

    cpfInput.addEventListener('blur', async () => {
        const cpf = cpfInput.value.replace(/\D/g, '');
        if (cpf.length < 11) return;
        await autofillFromCpf(cpf);
    });

    async function autofillFromCpf(cpf) {
        const nameInput = document.getElementById('name');
        const statusInput = document.getElementById('can_borrow');
        const classInput = document.getElementById('formatted_class_name');

        if (!nameInput || !statusInput || !classInput) return;

        if (cpf === lastCpf && lastResult) {
            applyStudentData(lastResult);
            return;
        }

        try {
            const response = await findStudentByCpf(cpf);
            const data = response?.data ?? null;

            lastCpf = cpf;
            lastResult = data;

            if (!data) {
                clearStudentFields();
                notifyError('Aluno não encontrado.');
                return;
            }

            applyStudentData(data);
        } catch {
            notifyError('Não foi possível carregar os dados do aluno.');
        }
    }
}

function applyStudentData(student) {
    const nameInput = document.getElementById('name');
    const statusInput = document.getElementById('can_borrow');
    const classInput = document.getElementById('formatted_class_name');

    nameInput.value = student.name ?? '';
    classInput.value = student.formatted_class_name ?? '';

    if (student.can_borrow === 1) {
        statusInput.value = 'Autorizado';
    } else if (student.can_borrow === 0) {
        statusInput.value = 'Bloqueado';
    } else {
        statusInput.value = '';
    }

    [nameInput, statusInput, classInput].forEach(el => {
        el.classList.toggle('has-value', !!el.value);
    });
}

function clearStudentFields() {
    const nameInput = document.getElementById('name');
    const statusInput = document.getElementById('can_borrow');
    const classInput = document.getElementById('formatted_class_name');

    [nameInput, statusInput, classInput].forEach(el => {
        el.value = '';
        el.classList.remove('has-value');
    });
}
