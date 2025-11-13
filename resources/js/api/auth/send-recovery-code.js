import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function sendCode(email) {
    const response = await api.post(route('recover.send'), { email: email.trim() });

    if (!response.ok) {
        console.warn('[Recover] Falha ao enviar código:', response.data);
    }

    return response;
}
