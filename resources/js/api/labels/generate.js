import { route } from 'ziggy-js';
import { api } from '../http-client.js';

/**
 * Generates labels via the API.
 *
 * @param {Object} data - Data required to generate the labels.
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function generateLabels(data) {
    return api.post(route('labels.generate'), data);
}
