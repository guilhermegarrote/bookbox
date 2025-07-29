import { route } from 'ziggy-js';

export async function resetPassword(data, csrfToken) {
    const response = await fetch(route('recover.reset.password'), {
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
