import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Validates a recovery code with the API.
 *
 * @param {string} code - The recovery code to validate.
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function validateCode(code) {
    return api.post(route('recover.validate.code'), { code });
}
