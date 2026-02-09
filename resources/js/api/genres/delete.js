import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Deletes a genre via the API.
 *
 * @param {number|string} genreId - The ID of the genre to delete.
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function deleteGenre(genreId) {
    return api.delete(route('genres.destroy', { genre: genreId }));
}
