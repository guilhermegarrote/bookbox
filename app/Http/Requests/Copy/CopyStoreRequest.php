<?php

namespace App\Http\Requests\Copy;

use Illuminate\Foundation\Http\FormRequest;

class CopyStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'isbn' => trim($this->input('isbn', '')),
            'number_copies' => (int) $this->input('number_copies', 1)
        ]);
    }

    public function rules(): array
    {
        return [
            'isbn' => [
                'required',
                'string',
                'regex:/^(97(8|9))?\d{9}(\d|X)$/i',
                'exists:books,isbn',
            ],
            'number_copies' => ['required', 'integer', 'min:1', 'max:32767']
        ];
    }

    public function messages(): array
    {
        return [
            'isbn.required' => 'O ISBN é obrigatório.',
            'isbn.string' => 'O ISBN deve ser um texto.',
            'isbn.regex' => 'O ISBN informado não tem um formato válido.',
            'isbn.exists' => 'O ISBN informado não foi encontrado na base de livros.',

            'number_copies.required' => 'O número de exemplares é obrigatório.',
            'number_copies.integer' => 'O número de exemplares deve ser um número inteiro.',
            'number_copies.min' => 'É necessário cadastrar pelo menos 1 exemplar.',
            'number_copies.max' => 'O número de exemplares não pode exceder 32767.'
        ];
    }
}
