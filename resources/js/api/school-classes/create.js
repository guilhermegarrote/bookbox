import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Creates a new school class via the API.
 *
 * @param {Object} data - School class data (e.g., course, start_date, end_date).
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function createSchoolClass(data) {
    return api.post(route('school-classes.store'), data);
}
