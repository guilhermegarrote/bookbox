import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function finalizeLoan(loanId) {
    const response = await api.patch(route('loans.finalize', { loan: loanId }));

    if (!response.ok) {
        console.warn('[Loans] Falha ao finalizar empréstimo:', response.status, response.data);
    }

    return response;
}
