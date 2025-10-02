import { route } from 'ziggy-js';

export async function updateStudent(studentId, data) {
    const url = route('students.update', { student: studentId });

    let response;
    try {
        response = await fetch(url, {
            method: 'PATCH',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(data),
        });
    } catch (err) {
        throw new Error('Erro de conexão ao atualizar aluno');
    }

    if (!response.ok) {
        const errorText = await response.text();
        throw new Error(errorText.message || 'Erro ao atualizar aluno');
    }

    if (response.status === 204) return null;

    return await response.json();
}
