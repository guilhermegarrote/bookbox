import { route } from 'ziggy-js';

export async function register(data, csrfToken) {
    const response = await fetch(route('api.register'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(data),
    });

    const responseData = await response.json();

    return { ok: response.ok, responseData, status: response.status };
}
