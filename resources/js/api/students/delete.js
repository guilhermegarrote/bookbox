import { route } from 'ziggy-js';
import { api } from '../http-client.js';

/**
 * Deletes a student via the API.
 *
 * @param {number|string} studentId - The ID of the student to delete.
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function deleteStudent(studentId) {
    return api.delete(route('students.destroy', { student: studentId }));
}
