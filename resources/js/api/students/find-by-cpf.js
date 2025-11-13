import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function findStudentByCpf(cpf) {
    const response = await api.get(route('students.findByCpf', { cpf }));

    if (!response.ok) {
        console.warn('[Students] Erro ao buscar aluno por CPF:', response.data);
    }

    return response;
}
