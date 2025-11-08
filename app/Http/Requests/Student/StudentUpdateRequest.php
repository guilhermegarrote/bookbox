<?php

declare(strict_types=1);

namespace App\Http\Requests\Student;

use App\Helpers\Utils;
use App\Helpers\Validators;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Handles validation for updating an existing student record.
 *
 * Responsibilities:
 * - Validates updates to student data (e.g., name, CPF, email, phone, etc.).
 * - Ensures unique identifiers (CPF, email, phone) excluding the current student.
 * - Sanitizes and normalizes input before validation.
 *
 * Security:
 * - Uses SHA-256 hashing for CPF, email, and phone to maintain privacy.
 *
 * @method mixed input(string $key, mixed $default = null) Retrieve an input item from the request.
 * @method void merge(array $input) Merge new input into the request's data.
 * @method mixed route(string|null $key = null, mixed $default = null) Retrieve a route parameter value.
 */
class StudentUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool true if the request is authorized
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define the validation rules for updating a student.
     *
     * Applies conditional validation using "sometimes" to allow partial updates.
     * Uniqueness checks for CPF, email, and phone exclude the current student's record.
     *
     * @return array<string, mixed> validation rules
     */
    public function rules(): array
    {
        $binaryId = Utils::convertUuidToBinary($this->route('student'));

        return [
            'name' => [
                'sometimes',
                'string',
                'max:100',
                function ($attribute, $value, $fail): void {
                    if (!Validators::validateFullName($value)) {
                        $fail('O nome deve conter entre 3 e 100 caracteres, com pelo menos nome e sobrenome. Apenas letras, espaços, hífens e apóstrofos são permitidos.');
                    }
                },
            ],
            'cpf' => [
                'sometimes',
                'string',
                'size:11',
                function ($attribute, $value, $fail) use ($binaryId): void {
                    if (!Validators::validateCpf($value)) {
                        $fail('O CPF é inválido.');

                        return;
                    }

                    $cpfHash = hash('sha256', $value, true);
                    $exists = DB::table('students')
                        ->where('cpf_hash', $cpfHash)
                        ->where('id', '!=', $binaryId)
                        ->exists()
                    ;

                    if ($exists) {
                        $fail('Este CPF já está vinculado a outro aluno.');
                    }
                },
            ],
            'email' => [
                'sometimes',
                'email',
                'max:100',
                function ($attribute, $value, $fail) use ($binaryId): void {
                    $emailHash = hash('sha256', strtolower($value), true);
                    $exists = DB::table('students')
                        ->where('email_hash', $emailHash)
                        ->where('id', '!=', $binaryId)
                        ->exists()
                    ;

                    if ($exists) {
                        $fail('Este e-mail já está vinculado a outro aluno.');
                    }
                },
            ],
            'phone' => [
                'sometimes',
                'string',
                'size:11',
                function ($attribute, $value, $fail) use ($binaryId): void {
                    if (!Validators::validatePhoneNumber($value)) {
                        $fail('Número de celular inválido. Certifique-se de que o número contenha 11 dígitos, começando com um DDD válido seguido de um número celular iniciado por 9.');

                        return;
                    }

                    if ($value) {
                        $phoneHash = hash('sha256', $value, true);
                        $exists = DB::table('students')
                            ->where('phone_hash', $phoneHash)
                            ->where('id', '!=', $binaryId)
                            ->exists()
                        ;

                        if ($exists) {
                            $fail('Este telefone já está vinculado a outro aluno.');
                        }
                    }
                },
            ],
            'course' => [
                'sometimes',
                function ($attribute, $value, $fail): void {
                    if (!Validators::validateCourseName($value)) {
                        $fail('O curso informado não foi reconhecido.');
                    }
                },
            ],
            'term' => [
                'sometimes',
                Rule::in(['Annual', 'Semester']),
            ],
            'period' => [
                'sometimes',
                'integer',
                'between:1,20',
            ],
        ];
    }

    /**
     * Get custom error messages for validation failures.
     *
     * @return array<string, string> custom validation messages
     */
    public function messages(): array
    {
        return [
            'name.string' => 'O nome deve ser um texto.',
            'name.max' => 'O nome não pode ter mais que 100 caracteres.',

            'cpf.string' => 'O CPF deve ser uma string.',
            'cpf.size' => 'O CPF deve conter exatamente 11 dígitos.',

            'email.email' => 'O e-mail informado é inválido. Verifique o formato (ex: nome@dominio.com).',
            'email.max' => 'O e-mail não pode ter mais que 100 caracteres.',

            'phone.string' => 'O telefone deve ser uma string.',
            'phone.size' => 'O telefone deve conter 11 dígitos, incluindo DDD. Ex: 11999998888.',

            'term.in' => 'Regime inválido. Os valores aceitos são: Anual ou Semestral.',

            'period.integer' => 'O período deve ser um número inteiro.',
            'period.between' => 'O período informado deve ser um número entre 1 e 20.',
        ];
    }

    /**
     * Prepare and normalize input data before validation.
     *
     * - Removes non-numeric characters from CPF, phone, and period.
     * - Trims whitespace from all text fields.
     * - Ensures consistent formatting of user input.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->input('name', '')),
            'cpf' => preg_replace('/\D/', '', $this->input('cpf', '')),
            'email' => trim($this->input('email', '')),
            'phone' => preg_replace('/\D/', '', $this->input('phone', '')),
            'course' => trim($this->input('course', '')),
            'term' => trim($this->input('term', '')),
            'period' => preg_replace('/\D/', '', $this->input('period', '')),
        ]);
    }
}
