import { route } from 'ziggy-js';
import { api } from '../http-client.js';

/**
 * Retrieves a list of loans from the API.
 *
 * @param {Object} [params={}] - Optional query parameters for filtering, pagination, etc.
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function sendRequest(params = {}) {
    return api.get(route('loans.index', params));
}
