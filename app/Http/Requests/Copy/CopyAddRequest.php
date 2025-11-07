<?php

declare(strict_types=1);

namespace App\Http\Requests\Copy;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request responsible for validating data when adding copies to a book.
 *
 * This request ensures that the provided ISBN exists in the database
 * and that the number of copies (amount) is a valid positive integer.
 *
 * @method string input(string $key, $default = null)
 * @method void merge(array $input)
 */
class CopyAddRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool true if the user is authorized to perform this request
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed> validation rules for the incoming request data
     */
    public function rules(): array
    {
        return [
            'isbn' => [
                'bail',
                'required',
                'string',
                'regex:/^(97(8|9))?\d{9}(\d|X)$/i',
                'exists:books,isbn',
            ],
            'amount' => [
                'required',
                'integer',
                'min:1',
                'max:32767',
            ],
        ];
    }

    /**
     * Get the custom messages for validation errors.
     *
     * @return array<string, string> custom messages for each validation rule
     */
    public function messages(): array
    {
        return [
            'isbn.required' => 'O ISBN é obrigatório.',
            'isbn.string' => 'O ISBN deve ser um texto.',
            'isbn.regex' => 'O ISBN informado não tem um formato válido.',
            'isbn.exists' => 'O ISBN informado não foi encontrado na base de livros.',

            'amount.required' => 'O número de exemplares é obrigatório.',
            'amount.integer' => 'O número de exemplares deve ser um número inteiro.',
            'amount.min' => 'É necessário cadastrar pelo menos 1 exemplar.',
            'amount.max' => 'O número de exemplares não pode exceder 32767.',
        ];
    }

    /**
     * Prepare the data for validation by sanitizing and normalizing inputs.
     *
     * This method trims string values and casts numeric inputs to the
     * appropriate type before the validation rules are applied.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'isbn' => trim($this->input('isbn', '')),
            'amount' => (int) $this->input('amount', 1),
        ]);
    }
}
