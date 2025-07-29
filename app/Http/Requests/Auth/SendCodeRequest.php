<?php

namespace App\Http\Requests\Auth;

use App\Helpers\Validators;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class SendCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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
                    $exists = DB::table('users')->where('email_hash', $emailHash)->exists();

                    if (!$exists) {
                        $fail('Este e-mail não está vinculado a um usuário.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O formato do e-mail é inválido.',
            'email.max' => 'O e-mail não pode ter mais que 320 caracteres.',
        ];
    }
}
