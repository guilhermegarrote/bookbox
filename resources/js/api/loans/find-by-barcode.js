import { route } from 'ziggy-js';

export async function findLoanByBarcode(barcode) {
    const url = route('loans.findByBarcode', { barcode });

    const response = await fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    });

    if (!response.ok) {
        const errorData = await response.json().catch(() => ({}));
        throw new Error(errorData.error.message || 'Erro ao consultar empréstimo pelo código de barras');
    }

    const data = await response.json();
    return data;
}
