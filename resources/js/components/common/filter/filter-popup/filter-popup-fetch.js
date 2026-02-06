/**
 * Filter Popup Fetcher
 * --------------------
 * Fetches filter HTML from backend endpoint.
 */

/**
 * Fetches the filter HTML from the backend.
 *
 * @async
 * @param {string} url - Endpoint that returns the filter HTML.
 * @returns {Promise<string>} HTML response string.
 * @throws {Error} If request fails.
 */
export const fetchFilterHTML = async url => {
    const response = await fetch(url, { credentials: 'same-origin' });

    if (!response.ok) {
        throw new Error('Erro ao carregar o filtro');
    }

    return response.text();
};
