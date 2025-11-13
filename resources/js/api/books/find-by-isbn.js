import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function findBookByIsbn(isbn) {
    const response = await api.get(route('books.findByIsbn', { isbn }));

    if (!response.ok) {
        console.warn('[Books] Falha ao buscar livro por ISBN:', isbn, response.data);
    }

    return response;
}
