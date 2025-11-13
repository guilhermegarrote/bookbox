import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function addCopies(bookId, data) {
    const response = await api.patch(route('books.addCopies', { book: bookId }), data);

    if (!response.ok) {
        console.warn('[Books] Falha ao adicionar exemplares:', response.status, response.data);
    }

    return response;
}
