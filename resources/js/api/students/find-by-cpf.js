import { route } from 'ziggy-js';

export async function findStudentByCpf(cpf) {
    const url = route('students.findByCpf', { cpf });

    const response = await fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    });

    if (!response.ok) {
        const errorData = await response.json().catch(() => ({}));
        throw new Error(errorData.error?.message || 'Erro ao consultar aluno pelo CPF');
    }

    const data = await response.json();
    return data;
}
