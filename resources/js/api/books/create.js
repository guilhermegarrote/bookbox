import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function createBook(data) {
    const response = await api.post(route('books.store'), data);

    if (!response.ok) {
        console.warn('[Books] Falha ao criar livro:', response.data);
    }

    return response;
}
