import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function resetPassword(data) {
    const response = await api.post(route('recover.reset.password'), data);

    if (!response.ok) {
        console.warn('[Recover] Falha ao redefinir senha:', response.data);
    }

    return response;
}
