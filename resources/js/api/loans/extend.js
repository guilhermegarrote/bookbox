import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function extendLoan(loanId) {
    const response = await api.patch(route('loans.extend', { loan: loanId }));

    if (!response.ok) {
        console.warn('[Loans] Falha ao prorrogar devolução:', response.status, response.data);
    }

    return response;
}
