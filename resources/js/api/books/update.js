import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function updateBook(bookId, data) {
    const response = await api.patch(route('books.update', { book: bookId }), data);

    if (!response.ok) {
        console.warn('[Books] Falha ao atualizar livro:', response.status, response.data);
    }

    return response;
}
