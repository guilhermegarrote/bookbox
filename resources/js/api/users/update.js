import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Updates a user via the API.
 *
 * @param {number|string} userId - The ID of the user to update.
 * @param {Object} data - The updated user data (name, email, password, password confirmation).
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function updateUser(userId, data) {
    return api.patch(route('users.update', { user: userId }), data);
}
