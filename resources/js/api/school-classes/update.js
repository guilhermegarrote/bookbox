import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Updates a school class via the API.
 *
 * @param {number|string} schoolClassId - The ID of the school class to update.
 * @param {Object} data - The updated school class data (e.g., course, start_date, end_date).
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function updateSchoolClass(schoolClassId, data) {
    return api.patch(route('school-classes.update', { 'school_class': schoolClassId }), data);
}
