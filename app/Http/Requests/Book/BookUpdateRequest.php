<?php

namespace App\Http\Requests\Book;

use App\Helpers\Utils;
use Illuminate\Foundation\Http\FormRequest;

class BookUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'isbn' => trim($this->input('isbn', '')),
            'title' => trim($this->input('title', '')),
            'author' => trim($this->input('author', '')),
            'publisher' => trim($this->input('publisher', '')),
            'genre_id' => $this->has('genre_id') && $this->input('genre_id') !== null
                ? Utils::convertUuidToBinary($this->input('genre_id'))
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'isbn' => [
                'sometimes',
                'string',
                'max:20',
                'regex:/^(97(8|9))?\d{9}(\d|X)$/i'
            ],
            'title' => ['sometimes', 'string', 'max:255'],
            'author' => ['sometimes', 'string', 'max:255'],
            'genre_id' => ['sometimes', 'exists:genres,id'],
            'publisher' => ['sometimes', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'isbn.string' => 'O ISBN deve ser um texto.',
            'isbn.max' => 'O ISBN não pode ter mais que 20 caracteres.',
            'isbn.regex' => 'O ISBN informado não é válido.',

            'title.string' => 'O título deve ser um texto.',
            'title.max' => 'O título não pode ter mais que 255 caracteres.',

            'author.string' => 'O autor deve ser um texto.',
            'author.max' => 'O nome do autor não pode ter mais que 255 caracteres.',

            'genre_id.exists' => 'O gênero selecionado não existe.',

            'publisher.string' => 'A editora deve ser um texto.',
            'publisher.max' => 'O nome da editora não pode ter mais que 255 caracteres.',
        ];
    }
}
