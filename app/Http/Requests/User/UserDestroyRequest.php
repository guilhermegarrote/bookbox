<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

/**
 * Handles validation logic for destroying an existing user.
 *
 * This request ensures that the password field is validated properly
 * and maintains data integrity during user deletion.
 *
 * @method mixed input(string $key, mixed $default = null) Retrieve an input item from the request.
 * @method void merge(array $input) Merge new input into the request's data.
 */
class UserDestroyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool true if the request is authorized
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define validation rules for updating a user.
     *
     * The method validates optional fields when present in the request payload.
     *
     * @return array<string, mixed> validation rules for user input fields
     */
    public function rules(): array
    {
        return [
            'password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, auth()->user()->password)) {
                        $fail('Senha incorreta. Você não tem permissão para excluir este usuário.');
                    }
                },
            ],
        ];
    }

    /**
     * Custom validation messages for the defined rules.
     *
     * @return array<string, string> error messages for validation failures
     */
    public function messages(): array
    {
        return [
            'password.required' => 'A senha é obrigatória.',
        ];
    }

    /**
     * Prepare input data before applying validation rules.
     *
     * Trims all string inputs to remove unnecessary whitespace
     * and ensures clean formatting for consistent validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'password' => trim($this->input('password', '')),
            'password_confirmation' => trim($this->input('password_confirmation', '')),
        ]);
    }
}
