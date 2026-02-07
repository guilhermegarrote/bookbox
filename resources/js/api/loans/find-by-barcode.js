import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Finds a loan by its barcode via the API.
 *
 * @param {string} barcode - The barcode of the loan to search for.
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function findLoanByBarcode(barcode) {
    return api.get(route('loans.findByBarcode', { barcode }));
}
