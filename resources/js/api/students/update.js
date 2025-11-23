import { route } from 'ziggy-js';
import { api } from '../http-client.js';

/**
 * Updates student information via the API.
 *
 * @param {number|string} studentId - The ID of the student to update.
 * @param {Object} data - The student data to update (e.g., name, email, etc.).
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function updateStudent(studentId, data) {
    return api.patch(route('students.update', { student: studentId }), data);
}
