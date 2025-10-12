import { route } from 'ziggy-js';

export async function fetchCreateModal() {
    const url = route('loans.createModal');

    const response = await fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'text/html',
        }
    });

    if (!response.ok) {
        throw new Error('Erro ao carregar modal de empréstimo: ' + response.status);
    }

    return await response.text();
}

export async function fetchMenuModal(loansId) {
    const url = route('loans.menuModal', { loans: loansId });

    const response = await fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'text/html',
        }
    });

    if (!response.ok) {
        throw new Error('Erro ao carregar modal de menu: ' + response.status);
    }

    return await response.text();
}
