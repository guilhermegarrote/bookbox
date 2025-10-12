import { createLoans } from '../../api/loans/create.js';
import { updateLoans } from '../../api/loans/update.js';
import { deleteLoans } from '../../api/loans/delete.js';
import loansTable from '../../pages/loans/table.js';
import { applyInputMasks } from '../../components/inputMask.js';
import ModalManager from '../../components/modalManager.js';
import { route } from 'ziggy-js';
import { showErrors, notifySuccess, notifyError } from '@/utils/formErrors';

// De onde tu tirou isso só Deus sabe:
// import { initloansSelects, openMenuModal } from '../loans/loans-modals.js';]
// tu importou a função do próprio arquiv dentro dele mesmo kkkkkkkkkkkkkkkkkkkkkk
//erro meu kkkkkkkkk
//e pro botao funcionar? onde que é

const modalManager = new ModalManager();

async function openCreateModal() {
    const url = route('loans.createModal');

    try {
        await modalManager.loadModalContent(url, 'loanCreateModal');

        modalManager.bindFormSubmit({
            modalId: 'loanCreateModal',
            buttonId: 'submit-create',
            onSubmit: createLoans,
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

async function openUpdateModal(loanId) {
    const url = route('loans.updateModal', { loan: loanId });

    try {
        await modalManager.loadModalContent(url, 'loanUpdateModal', {
            onInit: () => {
                applyInputMasks();
            }
        });

        document.getElementById('btn-cancel-update')?.addEventListener('click', () => {
            openMenuModal(loanId);
        });

        modalManager.bindFormSubmit({
            modalId: 'loanUpdateModal',
            buttonId: 'submit-update',
            onSubmit: (data) => updateLoans(loanId, data),
            onSuccess: () => {
                openMenuModal(loanId);
                loansTable.updateTable();
                notifySuccess('Empréstimo atualizado com sucesso!');
            },
            onError: (err) => {
                err.errors ? showErrors(err.errors) : notifyError(err.message || 'Erro ao atualizar empréstimo');
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

        document.getElementById('open-edit-modal')?.addEventListener('click', async () => {
            openUpdateModal(loanId);
        });

        document.getElementById('submit-delete')?.addEventListener('click', async () => {
            const confirmed = await modalManager.showModalMessage({
                message: 'Deseja realmente excluir este empréstimo?',
                acceptText: 'Sim',
                declineText: 'Cancelar'
            });

            if (!confirmed) return;

            try {
                await deleteLoans(loanId);
                modalManager.removeModal('loanMenuModal');
                loansTable.updateTable();
                notifySuccess('Empréstimo excluído com sucesso!');
            } catch (err) {
                notifyError(err.message || 'Erro ao excluir o empréstimo');
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
}

export function initLoansModals() {
    bindOpenButtons();
    document.addEventListener('tableUpdated', bindOpenButtons);
}

export { openCreateModal, openUpdateModal, openMenuModal };