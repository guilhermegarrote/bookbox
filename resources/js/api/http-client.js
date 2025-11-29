/**
 * Global HTTP handler for API requests.
 *
 * Handles API requests with the following features:
 * - Automatic inclusion of JWT cookies in requests
 * - Token refresh upon 401 (Unauthorized) response
 * - Redirects to login page if token refresh fails
 * - Automatic inclusion of CSRF token for security
 * - Throws errors on failure, allowing the calling code to handle them with try/catch
 *
 * @param {string} url - The URL to send the API request to.
 * @param {Object} [options={}] - Additional options to customize the request (e.g., headers, body, etc.).
 * @returns {Promise<Object>} - A promise that resolves to the API response object, including status and data.
 * @throws {Error} - Throws an error if the request fails, if the token refresh fails, or if the API response is unsuccessful.
 */
export async function apiFetch(url, options = {}) {
    const config = {
        credentials: 'include',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-XSRF-TOKEN': getCookie('XSRF-TOKEN') || '',
            ...(options.headers || {})
        },
        ...options
    };

    try {
        const start = performance.now();
        let response = await fetch(url, config);
        const time = (performance.now() - start).toFixed(1);
        console.info(`[API] ${config.method || 'GET'} ${url} → ${response.status} (${time}ms)`);

        if (response.status === 401) {
            console.warn('[Auth] Token expired. Trying refresh...');
            const refreshed = await tryRefreshToken();

            if (refreshed) {
                response = await fetch(url, config);
            } else {
                console.warn('[Auth] Session expired. Redirecting...');
                redirectToLogin();
                return makeErrorResponse('Sessão expirada', 401);
            }
        }

        const parsed = await safeParseJson(response);

        return parsed;
    } catch (err) {
        console.error('[API] Request error:', err);
        throw err;
    }
}

/**
 * Refreshes the JWT token by calling the /api/auth/refresh endpoint.
 *
 * @returns {boolean} - Returns true if the token was successfully refreshed, otherwise false.
 */
async function tryRefreshToken() {
    try {
        const res = await fetch('/api/auth/refresh', {
            method: 'POST',
            credentials: 'include',
        });
        return res.ok;
    } catch {
        return false;
    }
}

/**
 * Redirects the user to the login page.
 *
 * This function is called when the token refresh fails or the session expires.
 */
function redirectToLogin() {
    window.location.href = '/login';
}

/**
 * Safely parses a JSON or HTML response from the server.
 *
 * @param {Response} response - The response object to parse.
 * @returns {Promise<Object>} - The parsed response data, including status and data.
 */
async function safeParseJson(response) {
    const contentType = response.headers.get('content-type') || '';
    const contentLength = response.headers.get('content-length');

    if (contentLength === '0' || response.status === 204) {
        return { ok: response.ok, status: response.status, data: null };
    }

    let data = null;
    try {
        if (contentType.includes('application/json')) {
            const json = await response.json();

            const keys = Object.keys(json);
            if (keys.length === 1 && keys[0] === 'data') {
                data = json.data;
            } else {
                data = json;
            }
        } else if (contentType.includes('text/html')) {
            data = await response.text();
        }
    } catch (err) {
        console.error('Error parsing JSON:', err);
        data = null;
    }

    return { ok: response.ok, status: response.status, data };
}

/**
 * Creates a standardized error response.
 *
 * Used to format error responses when the request fails or encounters issues.
 *
 * @param {string} message - The error message to include in the response.
 * @param {number} [status=500] - The HTTP status code to return (default is 500).
 * @returns {Object} - A standardized error response object.
 */
function makeErrorResponse(message, status = 500) {
    return { ok: false, status, data: { error: message } };
}

/**
 * Retrieves a cookie value by its name.
 *
 * @param {string} name - The name of the cookie to retrieve.
 * @returns {string|null} - The value of the cookie, or null if it doesn't exist.
 */
function getCookie(name) {
    const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return match ? decodeURIComponent(match[2]) : null;
}

/**
 * HTTP method helpers for common request types (GET, POST, PUT, PATCH, DELETE).
 */
export const api = {
    /**
     * Sends a GET request.
     *
     * @param {string} url - The URL to send the GET request to.
     * @param {Object} [options={}] - Additional options for the request.
     * @returns {Promise<Object>} - The API response.
     */
    get: (url, options = {}) => apiFetch(url, { method: 'GET', ...options }),

    /**
     * Sends a POST request.
     *
     * @param {string} url - The URL to send the POST request to.
     * @param {Object} data - The data to send with the POST request.
     * @param {Object} [options={}] - Additional options for the request.
     * @returns {Promise<Object>} - The API response.
     */
    post: (url, data, options = {}) => apiFetch(url, { method: 'POST', body: JSON.stringify(data), ...options }),

    /**
     * Sends a PUT request.
     *
     * @param {string} url - The URL to send the PUT request to.
     * @param {Object} data - The data to send with the PUT request.
     * @param {Object} [options={}] - Additional options for the request.
     * @returns {Promise<Object>} - The API response.
     */
    put: (url, data, options = {}) => apiFetch(url, { method: 'PUT', body: JSON.stringify(data), ...options }),

    /**
     * Sends a PATCH request.
     *
     * @param {string} url - The URL to send the PATCH request to.
     * @param {Object} data - The data to send with the PATCH request.
     * @param {Object} [options={}] - Additional options for the request.
     * @returns {Promise<Object>} - The API response.
     */
    patch: (url, data, options = {}) => apiFetch(url, { method: 'PATCH', body: JSON.stringify(data), ...options }),

    /**
     * Sends a DELETE request.
     *
     * @param {string} url - The URL to send the DELETE request to.
     * @param {Object} [options={}] - Additional options for the request.
     * @returns {Promise<Object>} - The API response.
     */
    delete: (url, options = {}) => apiFetch(url, { method: 'DELETE', ...options }),
};
