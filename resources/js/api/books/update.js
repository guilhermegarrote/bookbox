import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Updates a book via the API.
 *
 * @param {number|string} bookId - The ID of the book to update.
 * @param {Object} data - The updated book data (e.g., title, author, ISBN).
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function updateBook(bookId, data) {
    return api.patch(route('books.update', { book: bookId }), data);
}
