import { route } from 'ziggy-js';
import { api } from '../http-client.js';

/**
 * Resends a recovery code via the API.
 *
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function resendCode() {
    return api.post(route('recover.resend'));
}
