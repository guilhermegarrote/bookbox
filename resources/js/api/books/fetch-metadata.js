import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function fetchMetadata(isbn) {
    const response = await api.get(route('books.fetchMetadata', isbn));

    if (!response.ok) {
        console.warn('[Books] Falha ao consultar dados do livro:', response.data);
    }

    return response;
}
