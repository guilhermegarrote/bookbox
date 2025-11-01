import { createLoan } from '../../api/loans/create.js';
import { findLoanByBarcode } from '../../api/loans/find-by-barcode.js';
import { extendLoan } from '../../api/loans/extend.js';
import { finalizeLoan } from '../../api/loans/finalize.js';
import { findStudentByCpf } from '../../api/students/find-by-cpf.js';
import { findBookByIsbn } from '../../api/books/find-by-isbn.js';
import loansTable from '../../pages/loans/table.js';
import { applyInputMasks } from '../../components/ui/input-mask.js';
import ModalManager from '../../components/managers/modal-manager.js';
import { route } from 'ziggy-js';
import { showErrors, notifySuccess, notifyError } from '@/utils/formErrors';

const modalManager = new ModalManager();

async function openCreateModal() {
    const url = route('loans.createModal');

    try {
        await modalManager.loadModalContent(url, 'loanCreateModal', {
            onInit: () => {
                applyInputMasks();
                setDefaultDueDate();
                initLoanAutoFill();
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
            onError: (err) => {
                err.errors ? showErrors(err.errors) : notifyError
                    (err.message || 'Erro ao cadastrar o empréstimo');
            }
        });
    } catch (err) {
        console.error(err);
    }
}

async function openMenuModal(loanId) {
    const url = route('loans.menuModal', { loan: loanId });

    try {
        await modalManager.loadModalContent(url, 'loanMenuModal');

        document.getElementById('open-extend-modal')?.addEventListener('click', async () => {
            const confirmed = await new Promise(async (resolve) => {
                const modalId = 'loanExtendModal';
                const url = route('loans.extendModal', { loan: loanId });

                await modalManager.loadModalContent(url, modalId);
                const modal = modalManager.activeModals.get(modalId);
                if (!modal) return resolve(false);

                modalManager.bindModalMessageEvents(modal, resolve);
            });

            if (!confirmed) return;

            try {
                await extendLoan(loanId);
                modalManager.removeModal('loanMenuModal');
                loansTable.updateTable();
                notifySuccess('Empréstimo prolongado com sucesso!');
            } catch (err) {
                notifyError(err.message || 'Erro ao prolongar o empréstimo');
            }
        });

        document.getElementById('submit-finalize')?.addEventListener('click', async () => {
            const confirmed = await modalManager.showModalMessage({
                message: 'Deseja realmente finalizar este empréstimo?',
                acceptText: 'Sim',
                declineText: 'Cancelar'
            });

            if (!confirmed) return;

            try {
                await finalizeLoan(loanId);
                modalManager.removeModal('loanMenuModal');
                loansTable.updateTable();
                notifySuccess('Empréstimo finalizado com sucesso!');
            } catch (err) {
                notifyError(err.message || 'Erro ao finalizar o empréstimo');
            }
        });
    } catch (err) {
        console.error(err);
    }
}

function bindOpenButtons() {
    const addBtn = document.querySelector('.btn-add');
    if (addBtn) addBtn.addEventListener('click', openCreateModal);

    document.querySelectorAll('table.data-table tbody tr').forEach(row => {
        row.addEventListener('click', () => {
            const id = row.getAttribute('data-loan-id');
            if (id) openMenuModal(id);
        });
    });

    document.querySelectorAll('.loan-list li').forEach(item => {
        item.addEventListener('click', () => {
            const id = item.getAttribute('data-loan-id');
            if (id) openMenuModal(id);
        });
    });
}

function initBarcodeScannerListener() {
    let barcodeBuffer = '';
    let barcodeTimer = null;

    document.addEventListener('keydown', async (e) => {
        const activeElement = document.activeElement;
        const modalOpen = document.querySelector('.modal.show') !== null;

        if (activeElement && ['INPUT', 'TEXTAREA', 'SELECT'].includes(activeElement.tagName)) return;
        if (modalOpen) return;

        if (e.key === 'Enter' && barcodeBuffer) {
            clearTimeout(barcodeTimer);

            try {
                const data = await findLoanByBarcode(barcodeBuffer);
                barcodeBuffer = '';

                if (data?.data?.loanId) {
                    openMenuModal(data.data.loanId);
                } else {
                    notifyError('Empréstimo não encontrado para o código lido.');
                }
            } catch (err) {
                console.error(err);
                notifyError(err.message || 'Erro ao processar o código.');
            }
        } else if (e.key.length === 1) {
            barcodeBuffer += e.key;
            clearTimeout(barcodeTimer);
            barcodeTimer = setTimeout(() => (barcodeBuffer = ''), 300);
        }
    });
}

export function initLoanAutoFill() {
    const cpfInput = document.getElementById('cpf');
    const isbnInput = document.getElementById('isbn');
    const nameInput = document.getElementById('name');
    const statusInput = document.getElementById('can_borrow');
    const classInput = document.getElementById('formatted_class_name');
    const titleInput = document.getElementById('title');
    const copySelect = document.getElementById('copy_number');

    if (!cpfInput || !isbnInput) return;

    let cpfTimeout, isbnTimeout;

    const fetchStudentData = async () => {
        const cpf = cpfInput.value.replace(/\D/g, '');
        if (!cpf) return;

        try {
            const result = await findStudentByCpf(cpf);

            if (result.data) {
                const data = result.data;
                nameInput.value = data.name || '';
                nameInput.classList.add('has-value');
                statusInput.value = data.can_borrow != null
                    ? (data.can_borrow === '1' ? 'Autorizado' : 'Bloqueado')
                    : '';
                statusInput.classList.add('has-value');
                classInput.value = data.formatted_class_name || '';
                classInput.classList.add('has-value');
            } else {
                nameInput.value = '';
                statusInput.value = '';
                classInput.value = '';
                notifyError('Aluno não encontrado.');
            }
        } catch (err) {
            console.error(err);
            notifyError('Não foi possível carregar os dados do aluno.');
        }
    };

    const fetchBookData = async () => {
        const isbn = isbnInput.value.replace(/\D/g, '');
        if (!isbn) return;

        try {
            const result = await findBookByIsbn(isbn);

            if (result.data) {
                const data = result.data;

                const book = data.book;
                const availableCopies = data.available_copies || [];

                titleInput.value = book.title || '';
                titleInput.classList.add('has-value');

                copySelect.innerHTML = '';

                if (availableCopies.length > 0) {
                    availableCopies.forEach(copy => {
                        const option = document.createElement('option');
                        option.value = copy.number;
                        option.textContent = copy.number;
                        copySelect.appendChild(option);
                    });

                    copySelect.value = availableCopies[0].number;
                    copySelect.classList.add('has-value');
                } else {
                    notifyError('Não há exemplares disponíveis deste livro.');
                }
            } else {
                titleInput.value = '';
                copySelect.innerHTML = '';
                notifyError('Livro não encontrado.');
            }
        } catch (err) {
            console.error(err);
            notifyError('Não foi possível carregar as informações do livro.');
        }
    };

    cpfInput.addEventListener('blur', () => {
        clearTimeout(cpfTimeout);
        cpfTimeout = setTimeout(fetchStudentData, 300);
    });

    isbnInput.addEventListener('blur', () => {
        clearTimeout(isbnTimeout);
        isbnTimeout = setTimeout(fetchBookData, 300);
    });
}

function setDefaultDueDate() {
    const input = document.getElementById('loan_due_date');
    if (!input) return;

    const dueDate = new Date();
    dueDate.setDate(dueDate.getDate() + 14);

    input.value = dueDate.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
    input.classList.add('has-value');
}

export function initLoansModals() {
    bindOpenButtons();
    initBarcodeScannerListener();
    document.addEventListener('tableUpdated', bindOpenButtons);
}

export { openCreateModal, openMenuModal };
