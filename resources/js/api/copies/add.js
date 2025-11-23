import { route } from 'ziggy-js';
import { api } from '../http-client.js';

/**
 * Adds copies to a book via the API.
 *
 * @param {number|string} bookId - The ID of the book to add copies to.
 * @param {Object} data - Data containing the number of copies to add.
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function addCopies(bookId, data) {
    return api.patch(route('books.addCopies', { book: bookId }), data);
}
