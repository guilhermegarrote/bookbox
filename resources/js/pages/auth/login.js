import { login } from '../../api/auth/login.js';
import { showErrors, clearErrors, notifyError } from '@/utils/formErrors';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('login-form');
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    if (!form) {
        console.error('Formulário de login não encontrado.');
        return;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors();

        if (!form.reportValidity()) {
            return;
        }

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        const validationErrors = validateForm(data);
        if (validationErrors.length > 0) {
            showErrors(validationErrors);
            return;
        }

        try {
            const { ok, responseData, status } = await login(data, csrfToken);

            if (!ok) {
                if (responseData.errors) {
                    showErrors(responseData.errors);
                } else if (status === 401) {
                    notifyError(responseData.error || 'Erro desconhecido.');
                }
                return;
            }

            if (responseData.data.user) {
                sessionStorage.setItem('user', JSON.stringify(responseData.user));
            }

            if (responseData.data.redirect) {
                window.location.href = responseData.data.redirect;
            }
        } catch (error) {
            console.error('Erro técnico no login:', error);
        }
    });

    const recoveryLink = document.querySelector('.auth-link');
    const emailInput = document.querySelector('input[name="email"]');

    recoveryLink.addEventListener('click', (e) => {
        e.preventDefault();

        const email = emailInput.value;
        if (email) {
            localStorage.setItem('recoveryEmail', email);
        }

        window.location.href = recoveryLink.getAttribute('href');
    });

    function validateForm(data) {
        const errors = [];

        if (!data.email || typeof data.email !== 'string' || data.email.trim() === '') {
            errors.push({ field: 'email', messages: ['O e-mail é obrigatório.'] });
        } else {
            const email = data.email.trim();
            if (email.length > 320) {
                errors.push({ field: 'email', messages: ['O e-mail não pode ter mais que 320 caracteres.'] });
            }
            if (!validateEmail(email)) {
                errors.push({ field: 'email', messages: ['Formato do e-mail inválido.'] });
            }
        }

        if (!data.password || typeof data.password !== 'string' || data.password.trim() === '') {
            errors.push({ field: 'senha', messages: ['A senha é obrigatória.'] });
        }

        return errors;
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
});
