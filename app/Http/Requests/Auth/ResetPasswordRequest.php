<?php

namespace App\Http\Requests\Auth;

use App\Helpers\Validators;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'password' => trim($this->input('password', '')),
            'password_confirmation' => trim($this->input('password_confirmation', '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'password' => [
                'required',
                'string',
                'min:8',
                'max:16',
                'confirmed',
                function ($attribute, $value, $fail) {
                    if (!Validators::validatePasswordStructure($value)) {
                        $fail('A senha deve ter de 8 a 16 caracteres, conter pelo menos 1 letra maiúscula, 1 minúscula, 1 número e 1 caractere especial.');
                    }
                }
            ],
        ];
    }

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
}
