import { route } from 'ziggy-js';
import { api } from '../http-client.js';

/**
 * Performs a login request to the API.
 *
 * @param {Object} data - User credentials (email and password).
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function login(data) {
    return api.post(route('api.login'), data);
}
