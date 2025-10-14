<?php

namespace App\Http\Requests\Book;

use App\Helpers\Utils;
use App\Helpers\Validators;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'isbn' => preg_replace('/\D/', '', trim($this->input('isbn', ''))),
            'title' => trim($this->input('title', '')),
            'author' => trim($this->input('author', '')),
            'publisher' => trim($this->input('publisher', '')),
            'genre_name' => trim($this->input('genre_name', '')),
        ]);
    }

    public function rules(): array
    {
        $binaryId = $this->route('book') ? Utils::convertUuidToBinary($this->route('book')) : null;

        return [
            'isbn' => [
                'sometimes',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    if (!Validators::validateIsbn($value)) {
                        $fail('O ISBN informado é inválido.');
                    }
                },
                Rule::unique('books', 'isbn')->ignore($binaryId, 'id'),
            ],
            'title' => ['sometimes', 'string', 'max:255', 'regex:/^[\pL\pN\s.,!?\'"-]+$/u'],
            'author' => ['sometimes', 'string', 'max:300', 'regex:/^[\pL\s.\'-]+$/u'],
            'publisher' => ['sometimes', 'string', 'max:150', 'regex:/^[\pL\s.\'-]+$/u'],
            'genre_name' => ['sometimes', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'isbn.string' => 'O ISBN deve ser um texto.',
            'isbn.max' => 'O ISBN não pode ter mais que 20 caracteres.',
            'isbn.regex' => 'O ISBN informado não é válido.',
            'isbn.unique' => 'Já existe outro livro com este ISBN.',

            'title.string' => 'O título deve ser um texto.',
            'title.max' => 'O título não pode ter mais que 255 caracteres.',
            'title.regex' => 'O título contém caracteres inválidos.',

            'author.string' => 'O autor deve ser um texto.',
            'author.max' => 'O nome do autor não pode ter mais que 300 caracteres.',
            'author.regex' => 'O nome do autor contém caracteres inválidos.',

            'publisher.string' => 'A editora deve ser um texto.',
            'publisher.max' => 'O nome da editora não pode ter mais que 150 caracteres.',
            'publisher.regex' => 'O nome da editora contém caracteres inválidos.',

            'genre_name.string' => 'O nome do gênero deve ser um texto.',
            'genre_name.max' => 'O nome do gênero não pode ter mais que 100 caracteres.',
        ];
    }
}
