import { route } from 'ziggy-js';
import { api } from '../http-client.js';

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
    const response = await api.post(route('api.logout'));

    sessionStorage.removeItem('user');
    localStorage.removeItem('recoveryEmail');

    window.location.href = route('login');

    return response;
}
