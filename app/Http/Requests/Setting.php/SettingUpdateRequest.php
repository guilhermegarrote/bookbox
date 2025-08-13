<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class SettingUpdateRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Pré-processa os dados antes da validação.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'key' => trim($this->input('key', '')),
            'value' => trim($this->input('value', '')),
        ]);
    }

    /**
     * Regras de validação.
     */
    public function rules(): array
    {
        return [
            'key' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9_.-]+$/', 
            ],
            'value' => [
                'sometimes',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Mensagens de erro personalizadas.
     */
    public function messages(): array
    {
        return [
            'key.string' => 'A chave deve ser um texto.',
            'key.max' => 'A chave não pode ter mais que 100 caracteres.',
            'key.regex' => 'A chave contém caracteres inválidos.',

            'value.string' => 'O valor deve ser um texto.',
            'value.max' => 'O valor não pode ter mais que 1000 caracteres.',
        ];
    }
}
