import { validateCode } from '@js/api/auth/recovery-code-validation.js';
import { resendCode } from '@js/api/auth/resend-recovery-code.js';
import { showErrors, clearErrors, notifyError, notifySuccess } from '@js/utils/formErrors';
import { validateRecoveryCode } from '@/utils/validation/auth-validation.js';
import '@css/pages/recovery-code-validation.css';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('recovery-code-validation-form');
    const resendLink = document.getElementById('resend-code-btn');
    const inputs = document.querySelectorAll('.verification-input');

    if (!form || !resendLink) {
        console.error('Elementos obrigatórios não encontrados.');
        return;
    }

    inputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            const value = e.target.value;
            if (!/^\d$/.test(value)) {
                e.target.value = '';
                return;
            }
            if (value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors();

        const code = Array.from(inputs).map(i => i.value).join('');
        const validationErrors = validateRecoveryCode(code);

        if (validationErrors.length) {
            showErrors(validationErrors);
            return;
        }

        try {
            const response = await validateCode(code);

            if (!response.ok) {
                if (response.data?.errors) showErrors(response.data.errors);
                else notifyError(response.data?.error || 'Erro ao validar o código.');
                return;
            }

            if (response.data?.redirect) {
                window.location.href = response.data.redirect;
            }
        } catch (err) {
            console.error('Erro ao validar código:', err);
            notifyError('Erro inesperado ao validar o código.');
        }
    });

    resendLink.addEventListener('click', async (e) => {
        e.preventDefault();

        try {
            const response = await resendCode();

            if (!response.ok) {
                if (response.data?.errors) showErrors(response.data.errors);
                else notifyError(response.data?.error || 'Erro ao reenviar o código.');
                return;
            }

            notifySuccess('Código reenviado com sucesso!');
        } catch (err) {
            console.error('Erro ao reenviar código:', err);
            notifyError('Erro inesperado ao reenviar o código.');
        }
    });
});
