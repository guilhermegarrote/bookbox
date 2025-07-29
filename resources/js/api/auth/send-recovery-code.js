import { route } from 'ziggy-js';

export async function sendCode(email, csrfToken) {
    const response = await fetch(route('recover.send'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ email: email.trim() }),
    });

    const responseData = await response.json();

    return { ok: response.ok, responseData, status: response.status };
}
