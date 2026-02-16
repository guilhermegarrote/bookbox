import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Updates settings via the API.
 *
 * @param {Object} settings - The updated settings data .
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function updateSettings(settings) {
    return api.put(route('settings.update'), { settings });
}
