import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Updates a book via the API.
 *
 * @param {number|string} genreId - The ID of the genre to update.
 * @param {Object} data - The updated genre data (e.g., name, description).
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function updateGenre(genreId, data) {
    return api.patch(route('genres.update', { genre: genreId }), data);
}
