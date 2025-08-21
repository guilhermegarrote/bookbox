<?php

namespace App\Http\Requests\Book;

use App\Helpers\Utils;
use Illuminate\Foundation\Http\FormRequest;

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
            'genre_id' => $this->has('genre_id') && $this->input('genre_id') !== null
                ? Utils::convertUuidToBinary($this->input('genre_id'))
                : null,
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
            ],
            'title' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\pN\s.,!?\'"-]+$/u',
            ],
            'author' => [
                'required',
                'string',
                'max:300',
                'regex:/^[\pL\s.\'-]+$/u',
            ],
            'genre_id' => ['required', 'exists:genres,id'],
            'publisher' => [
                'required',
                'string',
                'max:150',
                'regex:/^[\pL\s.\'-]+$/u',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'isbn.required' => 'O ISBN é obrigatório.',
            'isbn.string' => 'O ISBN deve ser um texto.',
            'isbn.max' => 'O ISBN não pode ter mais que 20 caracteres.',
            'isbn.regex' => 'O ISBN informado não é válido.',

            'title.required' => 'O título é obrigatório.',
            'title.string' => 'O título deve ser um texto.',
            'title.max' => 'O título não pode ter mais que 255 caracteres.',
            'title.regex' => 'O título contém caracteres inválidos.',

            'author.required' => 'O autor é obrigatório.',
            'author.string' => 'O autor deve ser um texto.',
            'author.max' => 'O nome do autor não pode ter mais que 300 caracteres.',
            'author.regex' => 'O nome do autor contém caracteres inválidos.',

            'genre_id.required' => 'O gênero é obrigatório.',
            'genre_id.exists' => 'O gênero selecionado não existe.',

            'publisher.required' => 'A editora é obrigatória.',
            'publisher.string' => 'A editora deve ser um texto.',
            'publisher.max' => 'O nome da editora não pode ter mais que 150 caracteres.',
            'publisher.regex' => 'O nome da editora contém caracteres inválidos.',
        ];
    }
}
