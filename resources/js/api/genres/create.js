import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Creates a new genre via the API.
 *
 * @param {Object} data - Genre data (e.g., name, color).
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function createGenre(data) {
    return api.post(route('genres.store'), data);
}
