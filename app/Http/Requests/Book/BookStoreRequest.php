<?php

namespace App\Http\Requests\Book;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookStoreRequest extends FormRequest
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
            'genre_name' => trim($this->input('genre_name', '')),
            'numberOfCopies' => (int) $this->input('numberOfCopies', 1),
        ]);
    }

    public function rules(): array
    {
        return [
            'isbn' => [
                'required',
                'string',
                'max:20',
                'regex:/^(97(8|9))?\d{9}(\d|X)$/i',
                Rule::unique('books', 'isbn'),
            ],
            'title' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\pN\s.,!?\'"()-]+$/u',
            ],
            'author' => [
                'required',
                'string',
                'max:300',
                'regex:/^[\pL\s.\'-]+$/u',
            ],
            'publisher' => [
                'required',
                'string',
                'max:150',
                'regex:/^[\pL\s.\'-]+$/u',
            ],
            'genre_name' => ['required', 'string', 'max:100'],
            'numberOfCopies' => ['required', 'integer', 'min:1', 'max:32767'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (isset($this->title, $this->author, $this->publisher)) {
                $exists = Book::where('title', $this->title)
                    ->where('author', $this->author)
                    ->where('publisher', $this->publisher)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('title', 'Já existe um livro com este título, autor e editora.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'isbn.required' => 'O ISBN é obrigatório.',
            'isbn.string' => 'O ISBN deve ser um texto.',
            'isbn.max' => 'O ISBN não pode ter mais que 20 caracteres.',
            'isbn.regex' => 'O ISBN informado não é válido.',
            'isbn.unique' => 'Já existe um livro com este ISBN.',

            'title.required' => 'O título é obrigatório.',
            'title.string' => 'O título deve ser um texto.',
            'title.max' => 'O título não pode ter mais que 255 caracteres.',
            'title.regex' => 'O título contém caracteres inválidos.',

            'author.required' => 'O autor é obrigatório.',
            'author.string' => 'O autor deve ser um texto.',
            'author.max' => 'O nome do autor não pode ter mais que 300 caracteres.',
            'author.regex' => 'O nome do autor contém caracteres inválidos.',

            'publisher.required' => 'A editora é obrigatória.',
            'publisher.string' => 'A editora deve ser um texto.',
            'publisher.max' => 'O nome da editora não pode ter mais que 150 caracteres.',
            'publisher.regex' => 'O nome da editora contém caracteres inválidos.',

            'genre_name.required' => 'O nome do gênero é obrigatório.',
            'genre_name.string' => 'O nome do gênero deve ser um texto.',
            'genre_name.max' => 'O nome do gênero não pode ter mais que 100 caracteres.',

            'numberOfCopies.required' => 'O número de cópias é obrigatório.',
            'numberOfCopies.integer' => 'O número de cópias deve ser um número inteiro.',
            'numberOfCopies.min' => 'O número de cópias deve ser no mínimo 1.',
            'numberOfCopies.max' => 'O número de cópias não pode exceder 32767.',
        ];
    }
}
