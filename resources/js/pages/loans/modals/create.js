import { route } from 'ziggy-js';
import { showErrors, notifySuccess, notifyError } from '@js/utils/formErrors';
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

const modalId = 'loanCreateModal';

/**
 * Opens the loan creation modal.
 *
 * @param {Object} modalManager
 * @param {Object} loansTable
 * @param {Object|null} initialData - Optional saved form state.
 * @returns {Promise<void>}
 */
export function openCreateModal(modalManager, loansTable, initialData = null) {
    return loadLoanModal(modalManager, loansTable, null, initialData);
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
    return loadLoanModal(modalManager, loansTable, isbn, initialData);
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
export async function loadLoanModal(modalManager, loansTable, isbn = null, initialData = null) {
    const url = route('loans.create-modal');
    try {
        _modalManagerRef = modalManager;
        _loansTableRef = loansTable;

        if (!_reopenListenerAttached) {
            window.addEventListener('reopenLoanModal', (e) => {
                const saved = e.detail;
                const data = saved?.data ?? null;

                if (_modalManagerRef && _loansTableRef) {
                    loadLoanModal(_modalManagerRef, _loansTableRef, null, data);
                }
            });

            _reopenListenerAttached = true;
        }

        await modalManager.loadModalContent(url, modalId, {
            onInit: () => {
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
            modalManager.saveModalState('loan:create:pending', modalId);
            openStudentsCreateModal(modalManager);
        });

        document.getElementById('open-create-book-modal')?.addEventListener('click', () => {
            modalManager.saveModalState('loan:create:pending', modalId);

            const isbn = document.getElementById('isbn').value.replace(/\D/g, '');

            if (isValidISBN(isbn)) {
                openBooksCreateModalWithIsbn(modalManager, '', isbn);
            } else {
                openBooksCreateModal(modalManager);
            }
        });

        modalManager.bindFormSubmit({
            modalId,
            buttonId: 'submit-create',
            onSubmit: createLoan,
            onSuccess: () => {
                modalManager.removeModal(modalId);
                loansTable.updateTable();
                notifySuccess('Empréstimo cadastrado com sucesso!');
            },
            onError: (err) => {
                err.errors ? showErrors(err.errors) : notifyError(err || 'Erro ao cadastrar o empréstimo');
            }
        });
    } catch (err) {
        console.error(err);
        notifyError('Erro ao carregar o modal de empréstimo.');
    }
}
