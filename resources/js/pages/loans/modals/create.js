import { route } from 'ziggy-js';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';
import { applyInputMasks } from '@js/components/ui/input-mask.js';
import { createLoan } from '@js/api/loans/create.js';
import { initCpfAutoFill } from './cpf-autofill';
import { initIsbnAutoFill } from './isbn-autofill';

export function openCreateModal(modalManager, loansTable) {
    return loadLoanModal(route('loans.createModal'), modalManager, loansTable);
}

export function openCreateModalWithIsbn(modalManager, loansTable, isbn) {
    return loadLoanModal(route('loans.createModal'), modalManager, loansTable, isbn);
}

export async function loadLoanModal(url, modalManager, loansTable, isbn = null) {
    try {
        await modalManager.loadModalContent(url, 'loanCreateModal', {
            onInit: () => {
                setDefaultDueDate();

                initCpfAutoFill();
                initIsbnAutoFill();

                if (isbn) {
                    const isbnInput = document.getElementById('isbn');
                    isbnInput.value = isbn;
                    isbnInput.classList.add('has-value');

                    isbnInput.dispatchEvent(new Event('keyup'));
                }

                applyInputMasks();
            }
        });

        modalManager.bindFormSubmit({
            modalId: 'loanCreateModal',
            buttonId: 'submit-create',
            onSubmit: createLoan,
            onSuccess: () => {
                modalManager.removeModal('loanCreateModal');
                loansTable.updateTable();
                notifySuccess('Empréstimo cadastrado com sucesso!');
            },
            onError: err => {
                if (err?.message) {
                    notifyError(err.message);
                } else {
                    notifyError('Erro ao cadastrar o empréstimo');
                }
            }
        });
    } catch (err) {
        console.error(err);
        notifyError('Erro ao carregar o modal de empréstimo.');
    }
}

function setDefaultDueDate() {
    const input = document.getElementById('loan_due_date');
    if (!input) return;

    const dueDate = new Date();
    dueDate.setDate(dueDate.getDate() + 14);

    input.value = dueDate.toLocaleDateString('pt-BR');
    input.classList.add('has-value');
}
