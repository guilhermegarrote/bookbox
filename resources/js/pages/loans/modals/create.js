import { route } from 'ziggy-js';
import { notifySuccess, notifyError } from '@js/utils/formErrors';
import { applyInputMasks } from '@js/components/ui/input-mask';
import { createLoan } from '@js/api/loans/create';
import { initCpfAutoFill } from './cpf-autofill';
import { initIsbnAutoFill } from './isbn-autofill';
import { isValidISBN } from '@js/utils/validation/app-validation';
import { openCreateModal as openBooksCreateModal, openCreateModalWithIsbn as openBooksCreateModalWithIsbn } from '@js/pages/books/modals/create';
import { openCreateModal as openStudentsCreateModal } from '@js/pages/students/modals/create';

let _modalManagerRef = null;
let _loansTableRef = null;
let _reopenListenerAttached = false;

/**
 * Opens the loan creation modal.
 *
 * @param {Object} modalManager
 * @param {Object} loansTable
 * @param {Object|null} initialData - Optional saved form state.
 * @returns {Promise<void>}
 */
export function openCreateModal(modalManager, loansTable, initialData = null) {
    return loadLoanModal(route('loans.create-modal'), modalManager, loansTable, null, initialData);
}

/**
 * Opens the loan creation modal and pre-fills the ISBN field.
 *
 * @param {Object} modalManager
 * @param {Object} loansTable
 * @param {string} isbn
 * @param {Object|null} initialData
 * @returns {Promise<void>}
 */
export function openCreateModalWithIsbn(modalManager, loansTable, isbn, initialData = null) {
    return loadLoanModal(route('loans.create-modal'), modalManager, loansTable, isbn, initialData);
}

/**
 * Loads the loan modal content and initializes all required behaviors.
 * Also binds a single global listener for reopening the modal.
 *
 * @async
 * @param {string} url - Backend route for modal HTML.
 * @param {Object} modalManager
 * @param {Object} loansTable
 * @param {string|null} isbn - Optional ISBN to pre-fill.
 * @param {Object|null} initialData - Saved form state to restore.
 * @returns {Promise<void>}
 */
export async function loadLoanModal(url, modalManager, loansTable, isbn = null, initialData = null) {
    try {
        _modalManagerRef = modalManager;
        _loansTableRef = loansTable;

        if (!_reopenListenerAttached) {
            window.addEventListener('reopenLoanModal', (e) => {
                const saved = e.detail;
                const data = saved?.data ?? null;

                if (_modalManagerRef && _loansTableRef) {
                    loadLoanModal(route('loans.create-modal'), _modalManagerRef, _loansTableRef, null, data);
                }
            });

            _reopenListenerAttached = true;
        }

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
            },

            initialData
        });

        document.getElementById('open-create-student-modal')?.addEventListener('click', () => {
            modalManager.saveModalState('loan:create:pending', 'loanCreateModal');
            openStudentsCreateModal(modalManager, loansTable);
        });

        document.getElementById('open-create-book-modal')?.addEventListener('click', () => {
            modalManager.saveModalState('loan:create:pending', 'loanCreateModal');

            const isbn = document.getElementById('isbn').value.replace(/\D/g, '');

            if (isValidISBN(isbn)) {
                openBooksCreateModalWithIsbn(modalManager, loansTable, isbn);
            } else {
                openBooksCreateModal(modalManager, loansTable);
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

/**
 * Sets the default due date to 14 days from today.
 * Updates the field #loan_due_date.
 *
 * @returns {void}
 */
function setDefaultDueDate() {
    const input = document.getElementById('loan_due_date');
    if (!input) return;

    const dueDate = new Date();
    dueDate.setDate(dueDate.getDate() + 14);

    input.value = dueDate.toLocaleDateString('pt-BR');
    input.classList.add('has-value');
}
