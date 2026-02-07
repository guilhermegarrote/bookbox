import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Creates a new book via the API.
 *
 * @param {Object} data - Book data (e.g., title, author, ISBN).
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function createBook(data) {
    return api.post(route('books.store'), data);
}
