import 'tippy.js/dist/tippy.css';
import tippy from 'tippy.js';

import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

const notyf = new Notyf({
    duration: 4000,
    position: {
        x: 'center',
        y: 'bottom',
    },
});

export function showErrors(errors) {
    if (!Array.isArray(errors) && typeof errors === 'object') {
        errors = Object.entries(errors).map(([field, messages]) => ({
            field,
            messages: Array.isArray(messages) ? messages : [messages]
        }));
    }

    if (!Array.isArray(errors)) return;

    errors.forEach(({ field, messages }) => {
        const input = document.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add('input-error');
            createTooltip(input, messages);
        }
    });
}

export function clearErrors() {
    document.querySelectorAll('.input-error').forEach(input => {
        input.classList.remove('input-error');
        if (input._tippy) {
            input._tippy.destroy();
        }
    });
}

function createTooltip(input, messages) {
    if (input._tippy) {
        input._tippy.destroy();
    }

    tippy(input, {
        content: messages.join('<br>'),
        allowHTML: true,
        trigger: 'manual',
        placement: 'right',
        theme: 'error',
    }).show();
}

export function notifySuccess(message) {
    notyf.success(message);
}

export function notifyError(message) {
    notyf.error(message);
}
