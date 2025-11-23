import { route } from 'ziggy-js';
import { api } from '../http-client.js';

/**
 * Registers a new user.
 *
 * @param {Object} data - User registration data (e.g., name, email, password).
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function register(data) {
    return api.post(route('api.register'), data);
}
