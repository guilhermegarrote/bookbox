import { route } from 'ziggy-js';
import { api } from '../http-client.js';

export async function logout() {
    try {
        const response = await api.post(route('api.logout'));

        if (!response.ok) {
            console.warn('[Auth] Falha ao fazer logout:', response.data);
        }

        sessionStorage.removeItem('user');
        localStorage.removeItem('recoveryEmail');

        window.location.href = route('login');

        return response;
    } catch (error) {
        console.error('[Auth] Erro ao fazer logout:', error);
        return { ok: false, error };
    }
}
