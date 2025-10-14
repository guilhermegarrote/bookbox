import { route } from 'ziggy-js';

export async function extendLoan(loanId) {
    const url = route('loans.extend', { loan: loanId });

    let response;
    try {
        response = await fetch(url, {
            method: 'PATCH',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        });
    } catch (err) {
        throw new Error('Erro de conexão ao prorrogar devolução do empréstimo');
    }

    if (!response.ok) {
        const errorText = await response.text();
        throw new Error(errorText.message || 'Erro ao prorrogar devolução do empréstimo');
    }

    if (response.status === 204) return null;

    return await response.json();
}
