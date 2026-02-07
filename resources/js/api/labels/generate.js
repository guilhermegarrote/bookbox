import { route } from 'ziggy-js';
import { api } from '../http-client';

/**
 * Generates labels via the API.
 *
 * @param {Object} data - Data required to generate the labels.
 * @returns {Promise<Object>} - API response object.
 * @throws {Error} - Throws if the network request or API call fails.
 */
export async function generateLabels(data) {
    const newWindow = window.open('', '_blank');

    const response = await api.post(route('labels.generate'), data);

    if (response?.data?.url) {
        newWindow.location.href = response.data.url;
    } else {
        newWindow.close();
    }

    return response;
}

