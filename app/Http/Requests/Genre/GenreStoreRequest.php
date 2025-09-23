<?php

namespace App\Http\Requests\Genre;

use Illuminate\Foundation\Http\FormRequest;

class GenreStoreRequest extends FormRequest
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
                ? strtoupper(ltrim(trim($this->input('color_hex')), '#'))
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:genres,name',
                'regex:/^[\pL\s.\'-]+$/u',
            ],
            'color_hex' => [
                'required',
                'string',
                'unique:genres,color_hex',
                'regex:/^[0-9A-Fa-f]{6}$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do gênero é obrigatório.',
            'name.string' => 'O nome do gênero deve ser um texto.',
            'name.max' => 'O nome do gênero não pode ter mais que 100 caracteres.',
            'name.unique' => 'Já existe um gênero com este nome.',
            'name.regex' => 'O nome do gênero contém caracteres inválidos.',

            'color_hex.required' => 'A cor é obrigatória.',
            'color_hex.string' => 'A cor deve ser um texto.',
            'color_hex.unique' => 'Já existe um gênero com esta cor.',
            'color_hex.regex' => 'A cor deve ser um código hexadecimal válido de 6 caracteres, ex: FF0000.',
        ];
    }
}
