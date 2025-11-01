import { route } from 'ziggy-js';

export async function sendRequest(params = {}) {
    const url = route('loans.index', params);

    const response = await fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
        }
    });

    if (!response.ok) {
        throw new Error('Erro na requisição: ' + response.status);
    }

    return await response.json();
}
