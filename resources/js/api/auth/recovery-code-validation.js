import { route } from 'ziggy-js';

export async function validateCode(code, csrfToken) {
    const response = await fetch(route('recover.validate.code'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ code: code }),
    });

    const responseData = await response.json();

    return { ok: response.ok, responseData, status: response.status };
}
