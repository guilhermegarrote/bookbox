import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Finalizes a loan via the API.
 *
 * @param {number|string} loanId - The ID of the loan to finalize.
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function finalizeLoan(loanId) {
    return api.patch(route('loans.finalize', { loan: loanId }));
}
