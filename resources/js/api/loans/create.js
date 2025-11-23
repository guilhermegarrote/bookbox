import { route } from 'ziggy-js';
import { api } from '../http-client.js';

/**
 * Creates a new loan via the API.
 *
 * @param {Object} data - Loan data (e.g., book_id, user_id, due_date).
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function createLoan(data) {
    return api.post(route('loans.store'), data);
}
