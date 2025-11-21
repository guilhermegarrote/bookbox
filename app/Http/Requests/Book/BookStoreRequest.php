<?php

declare(strict_types=1);

namespace App\Http\Requests\Book;

use App\Helpers\Validators;
use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Handles validation for creating a new book.
 *
 * @method string input(string $key, $default = null)
 * @method void merge(array $input)
 * @method mixed route(string $key = null, $default = null)
 */
class BookStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'isbn' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    if (!Validators::validateIsbn($value)) {
                        $fail('O ISBN informado é inválido.');
                    }
                },
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
                'regex:/^[\pL\s.\'\-\(\)]+$/u',
            ],
            'publisher' => [
                'required',
                'string',
                'max:150',
                'regex:/^[\pL\s.\'\-\(\)]+$/u',
            ],
            'genre_name' => ['required', 'string', 'max:100'],
            'number_copies' => ['required', 'integer', 'min:1', 'max:32767'],
        ];
    }

    /**
     * Add additional validation for duplicate book entries.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (isset($this->title, $this->author, $this->publisher)) {
                $exists = Book::where('title', $this->title)
                    ->where('author', $this->author)
                    ->where('publisher', $this->publisher)
                    ->exists()
                ;

                if ($exists) {
                    $validator->errors()->add('title', 'Já existe um livro com este título, autor e editora.');
                }
            }
        });
    }

    /**
     * Return custom validation messages in Portuguese.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'isbn.required' => 'O ISBN é obrigatório.',
            'isbn.string' => 'O ISBN deve ser um texto.',
            'isbn.max' => 'O ISBN não pode ter mais que 20 caracteres.',
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

            'number_copies.required' => 'O número de exemplares é obrigatório.',
            'number_copies.integer' => 'O número de exemplares deve ser um número inteiro.',
            'number_copies.min' => 'O número de exemplares deve ser no mínimo 1.',
            'number_copies.max' => 'O número de exemplares não pode exceder 32767.',
        ];
    }

    /**
     * Prepare and sanitize input data before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'isbn' => preg_replace('/\D/', '', trim($this->input('isbn', ''))),
            'title' => trim($this->input('title', '')),
            'author' => trim($this->input('author', '')),
            'publisher' => trim($this->input('publisher', '')),
            'genre_name' => trim($this->input('genre_name', '')),
            'number_copies' => (int) $this->input('number_copies', 1),
        ]);
    }
}
