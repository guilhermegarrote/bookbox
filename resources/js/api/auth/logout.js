import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function logout() {
    const response = await api.post(route('api.logout'));

    if (!response.ok) {
        console.warn('[Auth] Falha ao fazer logout:', response.data);
    }

    return response;
}
