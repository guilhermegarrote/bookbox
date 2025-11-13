/**
 * Global HTTP handler for API requests with:
 * - Automatic JWT cookies
 * - Token refresh on 401
 * - Redirect to login if refresh fails
 * - Automatic CSRF token
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

        return await safeParseJson(response);

    } catch (err) {
        console.error('[API] Network or fetch error:', err);
        return makeErrorResponse(err.message || 'Erro de conexão', 500);
    }
}

/** Refreshes the JWT token. */
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

/** Redirects to login page. */
function redirectToLogin() {
    window.location.href = '/login';
}

/** Safely parses JSON or HTML responses. */
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
            data = json.hasOwnProperty('data') ? json.data : json;
        } else if (contentType.includes('text/html')) {
            data = await response.text();
        }
    } catch {
        data = null;
    }

    return { ok: response.ok, status: response.status, data };
}

/** Creates a standardized error response. */
function makeErrorResponse(message, status = 500) {
    return { ok: false, status, data: { error: message } };
}

/** Retrieves a cookie by name. */
function getCookie(name) {
    const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return match ? decodeURIComponent(match[2]) : null;
}

/** HTTP method helpers. */
export const api = {
    get: (url, options = {}) => apiFetch(url, { method: 'GET', ...options }),
    post: (url, data, options = {}) => apiFetch(url, { method: 'POST', body: JSON.stringify(data), ...options }),
    put: (url, data, options = {}) => apiFetch(url, { method: 'PUT', body: JSON.stringify(data), ...options }),
    patch: (url, data, options = {}) => apiFetch(url, { method: 'PATCH', body: JSON.stringify(data), ...options }),
    delete: (url, options = {}) => apiFetch(url, { method: 'DELETE', ...options }),
};
