import { route } from 'ziggy-js';
import { api } from '../http-client.js';

/**
 * Deletes a copy via the API.
 *
 * @param {number|string} copyId - The ID of the copy to delete.
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function deleteCopy(copyId) {
    return api.delete(route('copies.destroy', { copy: copyId }));
}
