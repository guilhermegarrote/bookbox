<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Helpers\Validators;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

/**
 * Custom request for sending a password recovery code.
 *
 * @method \Illuminate\Session\Store session() Provides access to the session instance.
 */
class SendCodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool always true since this request does not require authentication
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed> validation rules for the email field
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:320',
                function ($attribute, $value, $fail) {
                    if (!Validators::validateEmail($value)) {
                        $fail('Este e-mail é inválido.');

                        return;
                    }

                    $emailHash = hash('sha256', strtolower($value), true);
                    $exists = DB::table('users')
                        ->where('email_hash', $emailHash)
                        ->exists()
                    ;

                    if (!$exists) {
                        $fail('Este e-mail não está vinculado a um usuário.');
                    }
                },
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string> custom validation messages
     */
    public function messages(): array
    {
        return [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O formato do e-mail é inválido.',
            'email.max' => 'O e-mail não pode ter mais que 320 caracteres.',
        ];
    }
}
