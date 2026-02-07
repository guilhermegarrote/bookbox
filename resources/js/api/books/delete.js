import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Deletes a book via the API.
 *
 * @param {number|string} bookId - The ID of the book to delete.
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function deleteBook(bookId) {
    return api.delete(route('books.destroy', { book: bookId }));
}
