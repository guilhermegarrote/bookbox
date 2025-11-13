import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function validateCode(code) {
    const response = await api.post(route('recover.validate.code'), {code});

    if (!response.ok) {
        console.warn('[Recover] Código de recuperação inválido:', response.data);
    }

    return response;
}
