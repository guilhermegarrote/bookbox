<?php

declare(strict_types=1);

namespace App\Http\Requests\Loan;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request responsible for validating data when storing a new loan.
 *
 * Prepares input data by sanitizing the CPF and ISBN, and validates
 * the presence and types of CPF, ISBN, and copy number.
 *
 * @method mixed input(string $key, mixed $default = null) Retrieve an input item from the request.
 * @method void merge(array $input) Merge new input into the request's data.
 *
 * @package App\Http\Requests\Loan
 */
class LoanStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool True if the user is authorized.
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
            'cpf' => ['required', 'string'],
            'isbn' => ['required', 'string'],
            'copy_number' => ['required', 'int'],
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
            'cpf.required' => 'O CPF do aluno é obrigatório.',
            'isbn.required' => 'O ISBN do livro é obrigatório.',
            'copy_number.required' => 'O número do exemplar é obrigatório.',
        ];
    }

    /**
     * Prepare input data before validation.
     *
     * Sanitizes the CPF by removing non-digits and the ISBN by
     * removing all characters except digits and 'X'.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'cpf' => preg_replace('/\D/', '', $this->input('cpf', '')),
            'isbn' => preg_replace('/[^0-9X]/', '', $this->input('isbn', '')),
        ]);
    }
}
