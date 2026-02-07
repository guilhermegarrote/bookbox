import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Sends a recovery code to the given email via the API.
 *
 * @param {string} email - The user's email address.
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function sendCode(email) {
    return api.post(route('recover.send'), { email: email.trim() });
}
