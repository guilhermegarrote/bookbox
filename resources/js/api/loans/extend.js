import { route } from 'ziggy-js';
import { api } from '../http-client.js';

/**
 * Extends the due date for a loan via the API.
 *
 * @param {number|string} loanId - The ID of the loan to extend.
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function extendLoan(loanId) {
    return api.patch(route('loans.extend', { loan: loanId }));
}
