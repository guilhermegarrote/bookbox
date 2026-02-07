import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Resets a user's password via the API.
 *
 * @param {Object} data - Password reset data (e.g., email, code, new password).
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function resetPassword(data) {
    return api.post(route('recover.reset.password'), data);
}
