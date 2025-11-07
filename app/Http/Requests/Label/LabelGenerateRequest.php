<?php

declare(strict_types=1);

namespace App\Http\Requests\Label;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request responsible for validating data when creating labels for books.
 *
 * Ensures that the input contains a valid list of books with ISBN and copies.
 *
 * @method mixed input(string $key, mixed $default = null) Retrieve an input item from the request.
 */
class LabelGenerateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool true if the user is authorized
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

    /**
     * Get custom error messages for validation failures.
     *
     * @return array<string, string>
     */
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
