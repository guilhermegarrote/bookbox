import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function findLoanByBarcode(barcode) {
    const response = await api.get(route('loans.findByBarcode', { barcode }));

    if (!response.ok) {
        console.warn('[Loans] Falha ao buscar empréstimo por código de barras:', response.status, response.data);
    }

    return response;
}
