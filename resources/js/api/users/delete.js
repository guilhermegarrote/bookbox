import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Deletes a user via the API.
 *
 * @param {number|string} userId - The ID of the user to delete.
 * @param {Object} data - Additional data to send with the delete request (e.g., password confirmation).
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function deleteUser(userId, data) {
    return api.delete(route('users.destroy', { user: userId }), data);
}
