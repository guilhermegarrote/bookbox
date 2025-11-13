import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function createStudent(data) {
    const response = await api.post(route('students.store'), data);

    if (!response.ok) {
        console.warn('[Students] Falha ao criar aluno:', response.data);
    }

    return response;
}
