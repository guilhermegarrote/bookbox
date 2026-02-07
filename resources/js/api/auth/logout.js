import { route } from 'ziggy-js';
import { api, clearAccessToken } from '../http-client';

/**
 * Logs out the current user.
 *
 * Sends a logout request to the API, clears session and local storage,
 * and redirects to the login page.
 *
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function logout() {
    try {
        await api.post(route('api.logout'));
    } finally {
        clearAccessToken();

        sessionStorage.removeItem('user');
        localStorage.removeItem('recoveryEmail');

        window.location.href = route('login');
    }
}
