import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Finds a student by CPF via the API.
 *
 * @param {string} cpf - The CPF of the student to search for.
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function findStudentByCpf(cpf) {
    return api.get(route('students.findByCpf', { cpf }));
}
