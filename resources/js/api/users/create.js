import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Creates a new user via the API.
 *
 * @param {Object} data - User data (name, email, password, password confirmation).
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function createUser(data) {
    return api.post(route('users.store'), data);
}
