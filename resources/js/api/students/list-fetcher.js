import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function sendRequest(params = {}) {
    const response = await api.get(route('students.index', params));

    if (!response.ok) {
        console.warn('[Students] Falha ao buscar lista de alunos:', response.status);
    }

    return response;
}
