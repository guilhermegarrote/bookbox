import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function createLoan(data) {
    const response = await api.post(route('loans.store'), data);

    if (!response.ok) {
        console.warn('[Loans] Falha ao cadastrar empréstimo:', response.status, response.data);
    }

    return response;
}
