<?php

namespace App\Http\Requests\Genre;

use Illuminate\Foundation\Http\FormRequest;

class GenreUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->input('name', '')),
            'color_hex' => $this->has('color_hex') && $this->input('color_hex') !== null
                ? strtoupper(trim($this->input('color_hex')))
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'string',
                'max:100',
                // Remove permissão para números
                'regex:/^[\pL\s.\'-]+$/u',
            ],
            'color_hex' => [
                'sometimes',
                'nullable',
                'string',
                // Agora valida 6 caracteres hex sem #
                'regex:/^[0-9A-Fa-f]{6}$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'O nome deve ser um texto.',
            'name.max' => 'O nome não pode ter mais que 100 caracteres.',
            'name.regex' => 'O nome contém caracteres inválidos.',

            'color_hex.string' => 'A cor deve ser um texto.',
            'color_hex.regex' => 'A cor deve estar no formato hexadecimal de 6 caracteres, por exemplo: A1B2C3.',
        ];
    }
}
