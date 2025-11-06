<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Helpers\Validators;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Handles validation for password reset requests.
 *
 * This request ensures that the new password meets security and
 * structural requirements before allowing an update.
 *
 * It trims whitespace, validates the password strength through
 * {@see Validators::validatePasswordStructure()}, and requires confirmation.
 *
 * @see \App\Http\Controllers\API\Auth\PasswordRecoveryController::resetPassword()
 *
 * @method \Illuminate\Session\Store session() Provides access to the session instance.
 * @method void merge(array $input = []) Merges additional input into the current request data.
 * @method mixed input(string|null $key = null, mixed $default = null) Retrieves input from the request.
 */
class ResetPasswordRequest extends FormRequest
{
    /**
     * Determines if the user is authorized to perform this request.
     *
     * Since password reset happens in a recovery flow, all users are allowed.
     *
     * @return bool always true for this request
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Defines the validation rules for the password reset process.
     *
     * @return array<string, array<int, mixed>> validation rules for the password field
     */
    public function rules(): array
    {
        return [
            'password' => [
                'required',
                'string',
                'min:8',
                'max:16',
                'confirmed',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (!Validators::validatePasswordStructure((string) $value)) {
                        $fail('A senha deve ter de 8 a 16 caracteres, conter pelo menos 1 letra maiúscula, 1 minúscula, 1 número e 1 caractere especial.');
                    }
                },
            ],
        ];
    }

    /**
     * Custom error messages for password validation.
     *
     * @return array<string, string> localized validation messages
     */
    public function messages(): array
    {
        return [
            'password.required' => 'A senha é obrigatória.',
            'password.string' => 'A senha deve ser uma string.',
            'password.min' => 'A senha deve conter no mínimo 8 caracteres.',
            'password.max' => 'A senha pode ter no máximo 16 caracteres.',
            'password.confirmed' => 'A confirmação de senha deve ser idêntica à senha informada.',
        ];
    }

    /**
     * Normalizes input before validation.
     *
     * This method trims whitespace from password fields to avoid validation
     * failures caused by accidental spaces.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'password' => trim((string) $this->input('password', '')),
            'password_confirmation' => trim((string) $this->input('password_confirmation', '')),
        ]);
    }
}
