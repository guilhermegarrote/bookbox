import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function register(data) {
    const response = await api.post(route('api.register'), data);

    if (!response.ok) {
        console.warn('[Auth] Falha ao registrar usuário:', response.data);
    }

    return response;
}
