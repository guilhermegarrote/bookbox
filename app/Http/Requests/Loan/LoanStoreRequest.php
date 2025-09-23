<?php

namespace App\Http\Requests\Loan;

use Illuminate\Foundation\Http\FormRequest;

class LoanStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cpf' => preg_replace('/\D/', '', $this->input('cpf', '')),
            'isbn' => preg_replace('/[^0-9X]/', '', $this->input('isbn', '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'cpf' => ['required', 'string'],
            'isbn' => ['required', 'string'],
            'copy_number' => ['required', 'int'],
        ];
    }

    public function messages(): array
    {
        return [
            'cpf.required' => 'O CPF do aluno é obrigatório.',
            'isbn.required' => 'O ISBN do livro é obrigatório.',
            'copy_number.required' => 'O número do exemplar é obrigatório.',
        ];
    }
}
