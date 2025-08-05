import { register } from '../../api/auth/register.js';
import { showErrors, clearErrors, notifyError } from '@/utils/formErrors';
import '../../../css/pages/auth.css';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('register-form');
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    if (!form) {
        console.error('Formulário de registro não encontrado.');
        return;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors();

        if (!form.reportValidity()) {
            return;
        }

        const formData = new FormData(form);
        const formValues = Object.fromEntries(formData.entries());

        const validationErrors = validateForm(formValues);
        if (validationErrors.length > 0) {
            showErrors(validationErrors);
            return;
        }

        try {
            const { ok, responseData } = await register(formValues, csrfToken);

            if (!ok) {
                if (responseData.errors) {
                    showErrors(responseData.errors);
                } else {
                    notifyError(responseData.error || responseData.message || 'Erro desconhecido.');
                }
                return;
            }

            if (responseData.data.redirect) {
                window.location.href = responseData.data.redirect;
            }
        } catch (error) {
            console.error('Erro ao registrar usuário:', error);
            notifyError('Erro inesperado ao redefinir a senha. Tente novamente.');
        }
    });

    function validateForm(data) {
        const errors = [];

        const name = data.name?.trim();
        if (!name) {
            errors.push({ field: 'name', messages: ['O nome é obrigatório.'] });
        } else {
            const nameErrors = [];
            if (name.length < 3 || name.length > 100) {
                nameErrors.push('O nome deve conter entre 3 e 100 caracteres.');
            }
            if (name.split(/\s+/).length < 2) {
                nameErrors.push('O nome deve conter pelo menos nome e sobrenome.');
            }
            if (!/^[A-Za-zÀ-ú\s'-]+$/.test(name)) {
                nameErrors.push('Apenas letras, espaços, hífens e apóstrofos são permitidos no nome.');
            }
            if (nameErrors.length > 0) {
                errors.push({ field: 'name', messages: nameErrors });
            }
        }

        const email = data.email?.trim();
        if (!email) {
            errors.push({ field: 'email', messages: ['O e-mail é obrigatório.'] });
        } else {
            const emailErrors = [];
            if (email.length > 320) {
                emailErrors.push('O e-mail não pode ter mais que 320 caracteres.');
            }
            if (!validateEmail(email)) {
                emailErrors.push('Formato do e-mail inválido.');
            }
            if (emailErrors.length > 0) {
                errors.push({ field: 'email', messages: emailErrors });
            }
        }

        const password = data.password?.trim();
        if (!password) {
            errors.push({ field: 'password', messages: ['A senha é obrigatória.'] });
        } else {
            const passwordErrors = [];
            if (password.length < 8 || password.length > 16) {
                passwordErrors.push('A senha deve ter entre 8 e 16 caracteres.');
            }
            if (!/[A-Z]/.test(password)) {
                passwordErrors.push('A senha deve conter pelo menos 1 letra maiúscula.');
            }
            if (!/[a-z]/.test(password)) {
                passwordErrors.push('A senha deve conter pelo menos 1 letra minúscula.');
            }
            if (!/[0-9]/.test(password)) {
                passwordErrors.push('A senha deve conter pelo menos 1 número.');
            }
            if (!/[^A-Za-z0-9]/.test(password)) {
                passwordErrors.push('A senha deve conter pelo menos 1 caractere especial.');
            }
            if (passwordErrors.length > 0) {
                errors.push({ field: 'password', messages: passwordErrors });
            }
        }

        const confirmation = data.password_confirmation?.trim();
        if (!confirmation) {
            errors.push({ field: 'password_confirmation', messages: ['A confirmação de senha é obrigatória.'] });
        } else if (confirmation !== password) {
            errors.push({ field: 'password_confirmation', messages: ['A confirmação de senha deve ser idêntica à senha informada.'] });
        }

        return errors;
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
});
