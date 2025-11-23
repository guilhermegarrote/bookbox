import { route } from 'ziggy-js';
import { api } from '../http-client.js';

/**
 * Creates a new student via the API.
 *
 * @param {Object} data - Student data (e.g., name, email, age).
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function createStudent(data) {
    return api.post(route('students.store'), data);
}
