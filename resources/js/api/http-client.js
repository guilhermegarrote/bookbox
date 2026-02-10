let accessToken = null;
let isRefreshing = false;
let refreshQueue = [];

/**
 * Stores the current JWT access token in memory.
 *
 * @param {string} token The JWT access token to store
 */
export function setAccessToken(token) {
    accessToken = token;
}

/**
 * Clears the stored JWT access token from memory.
 */
export function clearAccessToken() {
    accessToken = null;
}

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
    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-XSRF-TOKEN': getCookie('XSRF-TOKEN') || '',
        ...(options.headers || {}),
    };

    if (accessToken) {
        headers.Authorization = `Bearer ${accessToken}`;
    }

    const config = {
        credentials: 'include',
        headers,
        ...options,
    };

    try {
        let response = await fetch(url, config);

        if (response.status === 401 && (!url.includes('/auth/refresh') && !url.includes('/auth/login'))) {
            console.warn('[Auth] Access token expirado. Tentando refresh...');

            const refreshed = await handleRefresh();

            if (!refreshed) {
                console.warn('[Auth] Sessão expirada. Redirecionando para login...');
                clearAccessToken();
                redirectToLogin();
                return makeErrorResponse('Sessão expirada', 401);
            }

            response = await fetch(url, config);
        }

        return await safeParseJson(response);
    } catch (err) {
        console.error('[API] Erro de requisição:', err);
        throw err;
    }
}

/**
 * Attempts to refresh the JWT access token by calling the `/api/auth/refresh` endpoint.
 *
 * Ensures only one refresh request runs at a time. Any concurrent calls
 * while a refresh is in progress are queued and resolved once the refresh completes.
 *
 * On success, updates the stored access token via `setAccessToken`.
 *
 * @async
 * @returns {Promise<boolean>} Resolves to `true` if the token was successfully refreshed, `false` otherwise.
 */
async function handleRefresh() {
    if (isRefreshing) {
        return new Promise((resolve) => refreshQueue.push(resolve));
    }

    isRefreshing = true;

    try {
        const res = await fetch('/api/auth/refresh', {
            method: 'POST',
            credentials: 'include',
            headers: { 'Accept': 'application/json' },
        });

        if (!res.ok) return false;

        const json = await res.json();

        if (json?.data?.token) {
            setAccessToken(json.data.token);
        } else {
            return false;
        }

        refreshQueue.forEach((resolve) => resolve(true));
        refreshQueue = [];

        return true;
    } catch {
        refreshQueue.forEach((resolve) => resolve(false));
        refreshQueue = [];
        return false;
    } finally {
        isRefreshing = false;
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

    try {
        if (contentType.includes('application/json')) {
            const json = await response.json();
            return {
                ok: response.ok,
                status: response.status,
                data:
                    Object.keys(json).length === 1 && json.hasOwnProperty('data')
                        ? json.data
                        : json,
            };
        }

        if (contentType.includes('text/html')) {
            return {
                ok: response.ok,
                status: response.status,
                data: await response.text(),
            };
        }
    } catch (err) {
        console.error('[API] Erro ao parsear resposta:', err);
    }

    return { ok: false, status: response.status, data: null };
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
