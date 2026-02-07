import { route } from 'ziggy-js';
import { finalizeLoan } from '@js/api/loans/finalize';
import { extendLoan } from '@js/api/loans/extend';
import { notifySuccess, notifyError } from '@js/utils/formErrors';

/**
 * Opens the loan menu modal and binds action handlers.
 *
 * @async
 * @param {Object} modalManager - Modal controller instance.
 * @param {number|string} loanId - Loan ID to manage.
 * @param {Object} loansTable - Table instance used to refresh results.
 * @returns {Promise<void>}
 */
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
                const { ok, data: responseData } = await extendLoan(loanId);

                if (!ok) {
                    notifyError(responseData?.error || 'Erro ao prolongar o empréstimo');
                } else {
                    modalManager.removeModal('loanMenuModal');
                    loansTable.updateTable();
                    notifySuccess('Empréstimo prolongado com sucesso!');
                }
            } catch (err) {
                console.error(err);
                notifyError('Erro técnico ao tentar prolongar o empréstimo. Tente novamente.');
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
                const { ok, data: responseData } = await finalizeLoan(loanId);

                if (!ok) {
                    notifyError(responseData?.error || 'Erro ao finalizar o empréstimo');
                } else {
                    modalManager.removeModal('loanMenuModal');
                    loansTable.updateTable();
                    notifySuccess('Empréstimo finalizado com sucesso!');
                }
            } catch (err) {
                console.error(err);
                notifyError('Erro técnico ao tentar finalizar o empréstimo. Tente novamente.');
            }
        });
    } catch (err) {
        console.error(err);
    }
}
