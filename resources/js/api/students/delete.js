import { route } from 'ziggy-js';

export async function deleteStudent(studentId) {
    const url = route('students.destroy', { student: studentId });

    const response = await fetch(url, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    });

    if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Erro ao deletar aluno');
    }

    return await response.json();
}
