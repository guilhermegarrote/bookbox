import { route } from 'ziggy-js';

export async function findBookByIsbn(isbn) {
    const url = route('books.findByIsbn', { isbn });

    const response = await fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    });

    if (!response.ok) {
        const errorData = await response.json().catch(() => ({}));
        throw new Error(errorData.error?.message || 'Erro ao consultar livro pelo ISBN');
    }

    const data = await response.json();
    return data;
}
