import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function updateStudent(studentId, data) {
    const response = await api.patch(route('students.update', { student: studentId }), data);

    if (!response.ok) {
        console.warn('[Students] Falha ao atualizar aluno:', response.status, response.data);
    }

    return response;
}
