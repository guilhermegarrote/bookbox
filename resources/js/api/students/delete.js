import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function deleteStudent(studentId) {
    const response = await api.delete(route('students.destroy', { student: studentId }));

    if (!response.ok) {
        console.warn('[Students] Falha ao deletar aluno:', response.data);
    }

    return response;
}
