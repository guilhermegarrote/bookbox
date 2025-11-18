import { register } from '@js/api/auth/register.js';
import { showErrors, clearErrors, notifyError } from '@js/utils/formErrors';
import '@css/pages/auth.css';

import {
    validateNameField,
    validateEmailField,
    validatePasswordField,
    validatePasswordConfirmation,
    validateTermsField,
    combineValidations
} from '@/utils/validation/auth-validation.js';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('register-form');
    if (!form) {
        console.error('Formulário de registro não encontrado.');
        return;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors();

        if (!form.reportValidity()) return;

        const formData = new FormData(form);
        const formValues = Object.fromEntries(formData.entries());

        const validationErrors = combineValidations(
            validateNameField(formValues.name),
            validateEmailField(formValues.email),
            validatePasswordField(formValues.password),
            validatePasswordConfirmation(formValues.password, formValues.password_confirmation),
            validateTermsField()
        );

        if (validationErrors.length > 0) {
            showErrors(validationErrors);
            return;
        }

        try {
            const response = await register(formValues);

            if (!response.ok) {
                if (response.data?.errors) {
                    showErrors(response.data.errors);
                } else {
                    notifyError(response.data?.error || response.data?.message || 'Erro desconhecido.');
                }
                return;
            }

            if (response.data?.redirect) {
                window.location.href = response.data.redirect;
            }
        } catch (error) {
            console.error('Erro ao registrar usuário:', error);
            notifyError('Erro inesperado ao registrar o usuário. Tente novamente.');
        }
    });
});
