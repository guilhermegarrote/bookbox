<?php

namespace App\Http\Requests\Label;

use Illuminate\Foundation\Http\FormRequest;

class LabelStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'books' => ['required', 'array'],
            'books.*.isbn' => [
                'required',
                'string',
                'max:20',
                'regex:/^(97(8|9))?\d{9}(\d|X)$/i', 
            ],
            'books.*.copies' => [
                'required',
                'string',
                'regex:/^(\d+(-\d+)?)(,\d+(-\d+)?)*$/', 
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'books.required' => 'A lista de livros é obrigatória.',
            'books.array' => 'A estrutura dos livros deve ser uma lista.',

            'books.*.isbn.required' => 'O ISBN é obrigatório.',
            'books.*.isbn.string' => 'O ISBN deve ser um texto.',
            'books.*.isbn.max' => 'O ISBN não pode ter mais de 20 caracteres.',
            'books.*.isbn.regex' => 'O ISBN informado não é válido.',

            'books.*.copies.required' => 'A lista de exemplares é obrigatória.',
            'books.*.copies.string' => 'A lista de exemplares deve ser um texto.',
            'books.*.copies.regex' => 'O formato dos exemplares é inválido. Use formatos como "1-3,5".',
        ];
    }
}
