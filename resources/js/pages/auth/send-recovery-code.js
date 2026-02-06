import { sendCode } from '@js/api/auth/send-recovery-code.js';
import { showErrors, clearErrors, notifyError } from '@js/utils/formErrors';
import '@css/pages/auth.css';

import { validateEmailField } from '@/utils/validation/auth-validation.js';

/**
 * Handles sending recovery code form validation and submission.
 */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('send-recovery-code-form');

    if (!form) {
        console.error('Formulário de envio de código não encontrado.');
        return;
    }

    const emailInput = form.querySelector('input[name="email"]');

    const savedEmail = localStorage.getItem('recoveryEmail');

    if (savedEmail && emailInput) {
        emailInput.value = savedEmail;
        localStorage.removeItem('recoveryEmail');
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors();

        if (!form.reportValidity()) return;

        const email = emailInput?.value?.trim() || '';
        const validationErrors = validateEmailField(email);

        if (validationErrors.length > 0) {
            showErrors(validationErrors);
            return;
        }

        try {
            const { ok, data } = await sendCode(email);

            if (!ok) {
                if (data.errors) {
                    showErrors(data.errors);
                } else {
                    notifyError(data.error || data.message || 'Erro desconhecido.');
                }
                return;
            }

            if (data.redirect) {
                window.location.href = data.redirect;
            }
        } catch (error) {
            console.error('Erro ao enviar código:', error);
            notifyError('Erro inesperado ao enviar o código. Tente novamente.');
        }
    });
});
