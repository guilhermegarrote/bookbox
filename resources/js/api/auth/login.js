import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function login(data) {
    const response = await api.post(route('api.login'), data);

    if (!response.ok) {
        console.warn('[Login] Falha no login:', response.data);
    }

    return response;
}
