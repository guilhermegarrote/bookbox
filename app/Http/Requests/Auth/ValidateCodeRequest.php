<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Custom request for validating a password recovery code.
 *
 * @method \Illuminate\Session\Store session() Provides access to the session instance.
 */
class ValidateCodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool Always true because password validation does not require authentication
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define the validation rules.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'regex:/^\d{6}$/'],
        ];
    }

    /**
     * Define custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'O código é obrigatório.',
            'code.regex' => 'O código deve conter exatamente 6 dígitos numéricos.',
        ];
    }
}
