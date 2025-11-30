import { route } from 'ziggy-js';
import { finalizeLoan } from '@js/api/loans/finalize.js';
import { extendLoan } from '@js/api/loans/extend.js';
import { notifySuccess, notifyError } from '@/utils/formErrors';

export async function openMenuModal(modalManager, loanId, loansTable) {
    const url = route('loans.menu-modal', { loan: loanId });

    try {
        await modalManager.loadModalContent(url, 'loanMenuModal');

        document.getElementById('open-extend-modal')?.addEventListener('click', async () => {
            const confirmed = await new Promise(async (resolve) => {
                const modalId = 'loanExtendModal';
                const url = route('loans.extend-modal', { loan: loanId });

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

