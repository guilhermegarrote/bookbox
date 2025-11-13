import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function generateLabels(data) {
    const response = await api.post(route('labels.generate'), data);

    if (!response.ok) {
        console.warn('[Labels] Falha ao gerar etiquetas:', response.status, response.data);
    }

    return response;
}
