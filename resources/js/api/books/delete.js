import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function deleteBook(bookId) {
    const response = await api.delete(route('books.destroy', { book: bookId }));

    if (!response.ok) {
        console.warn('[Books] Falha ao deletar livro:', response.data);
    }

    return response;
}
