import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function resendCode() {
    const response = await api.post(route('recover.resend'));

    if (!response.ok) {
        console.warn('[Recover] Falha ao reenviar código de recuperação:', response.data);
    }

    return response;
}
