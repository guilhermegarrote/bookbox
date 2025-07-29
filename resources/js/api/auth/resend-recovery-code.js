import { route } from 'ziggy-js';

export async function resendCode(csrfToken) {
    const response = await fetch(route('recover.resend'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
    });

    const responseData = await response.json();

    return { ok: response.ok, responseData, status: response.status };
}
