import { resetPassword } from '@js/api/auth/reset-password.js';
import { showErrors, clearErrors, notifyError } from '@js/utils/formErrors';
import '@css/pages/auth.css';

import {
    validatePasswordField,
    validatePasswordConfirmation,
    combineValidations
} from '@/utils/validation/auth-validation.js';

/**
 * Handles password reset form validation and submission.
 */
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('reset-password-form');

    if (!form) {
        console.error('Formulário de redefinição de senha não encontrado.');
        return;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors();

        if (!form.reportValidity()) return;

        const formData = new FormData(form);
        const formValues = Object.fromEntries(formData.entries());

        const validationErrors = combineValidations(
            validatePasswordField(formValues.password),
            validatePasswordConfirmation(formValues.password, formValues.password_confirmation)
        );

        if (validationErrors.length > 0) {
            showErrors(validationErrors);
            return;
        }

        try {
            const { ok, data } = await resetPassword(formValues);

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
            console.error('Erro ao redefinir senha:', error);
            notifyError('Erro inesperado ao redefinir a senha. Tente novamente.');
        }
    });
});
