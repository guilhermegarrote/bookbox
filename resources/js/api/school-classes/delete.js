import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Deletes a school class via the API.
 *
 * @param {number|string} schoolClassId - The ID of the school class to delete.
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function deleteSchoolClass(schoolClassId) {
    return api.delete(route('school-classes.destroy', { school_class: schoolClassId }));
}
