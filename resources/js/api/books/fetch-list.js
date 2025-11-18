import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function sendRequest(params = {}) {
    const response = await api.get(route('books.index', params));

    if (!response.ok) {
        console.warn('[Books] Falha ao listar livros:', response.data);
    }

    return response;
}
