import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Fetches metadata for a book by ISBN from the API.
 *
 * @param {string} isbn - The ISBN of the book.
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function fetchMetadata(isbn) {
    return api.get(route('books.fetchMetadata', isbn));
}
