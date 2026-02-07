import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Finds a book by its ISBN via the API.
 *
 * @param {string} isbn - The ISBN of the book to search for.
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function findBookByIsbn(isbn) {
    return api.get(route('books.findByIsbn', { isbn }));
}
