import { route } from 'ziggy-js';

export async function fetchCreateModal() {
    const url = route('students.createModal');

    const response = await fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'text/html',
        }
    });

    if (!response.ok) {
        throw new Error('Erro ao carregar modal de cadastro: ' + response.status);
    }

    return await response.text();
}

export async function fetchMenuModal(studentId) {
    const url = route('students.menuModal', { student: studentId });

    const response = await fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'text/html',
        }
    });

    if (!response.ok) {
        throw new Error('Erro ao carregar modal de menu: ' + response.status);
    }

    return await response.text();
}
