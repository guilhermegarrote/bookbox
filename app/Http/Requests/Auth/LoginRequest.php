<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Handles validation for user login requests.
 *
 * This request ensures that the email and password fields
 * are provided and conform to basic authentication requirements.
 *
 * @see \App\Http\Controllers\API\Auth\LoginController
 *
 * @method string ip() Returns the IP address of the request origin.
 */
class LoginRequest extends FormRequest
{
    /**
     * Determines if the user is authorized to make this request.
     *
     * In this case, all users are allowed since login does not require authentication.
     *
     * @return bool always true for login
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Defines the validation rules applied to the login request.
     *
     * @return array<string, array<int, string>> validation rules for email and password fields
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:320'],
            'password' => ['required', 'string', 'min:8', 'max:16'],
        ];
    }

    /**
     * Provides custom error messages for rule violations.
     *
     * @return array<string, string> translated validation messages
     */
    public function messages(): array
    {
        return [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser válido.',
            'email.max' => 'O e-mail deve ter no máximo 320 caracteres.',

            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.max' => 'A senha deve ter no máximo 16 caracteres.',
        ];
    }
}
