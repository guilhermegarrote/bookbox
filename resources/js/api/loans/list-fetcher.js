import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function sendRequest(params = {}) {
    const response = await api.get(route('loans.index', params));

    if (!response.ok) {
        console.warn('[Loans] Falha ao buscar empréstimos:', response.status, response.data);
    }

    return response;
}
