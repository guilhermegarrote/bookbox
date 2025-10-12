import { route } from 'ziggy-js';

export async function fetchCreateModal() {
    const url = route('books.createModal');

    const response = await fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'text/html',
        }
    });

    if (!response.ok) {
        throw new Error('Erro ao carregar modal de cadastro: ' + response.status);
    }

    return await response.text();
}

export async function fetchMenuModal(booksId) {
    const url = route('books.menuModal', { books: booksId });

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
