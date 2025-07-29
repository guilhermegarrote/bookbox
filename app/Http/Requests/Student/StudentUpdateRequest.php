<?php

namespace App\Http\Requests\Student;

use App\Helpers\Utils;
use App\Helpers\Validators;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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

    public function rules(): array
    {
        $binaryId = Utils::convertUuidToBinary($this->route('student'));

        return [
            'name' => [
                'sometimes',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    if (!Validators::validateFullName($value)) {
                        $fail('O nome deve conter entre 3 e 100 caracteres, com pelo menos nome e sobrenome. Apenas letras, espaços, hífens e apóstrofos são permitidos.');
                    }
                }
            ],
            'cpf' => [
                'sometimes',
                'string',
                'size:11',
                function ($attribute, $value, $fail) use ($binaryId) {
                    if (!Validators::validateCpf($value)) {
                        $fail('O CPF é inválido.');
                        return;
                    }
                    $cpfHash = hash('sha256', $value, true);
                    $exists = DB::table('students')
                        ->where('cpf_hash', $cpfHash)
                        ->where('id', '!=', $binaryId)
                        ->exists();
                    if ($exists) {
                        $fail('Este CPF já está vinculado a outro aluno.');
                    }
                }
            ],
            'email' => [
                'sometimes',
                'email',
                'max:100',
                function ($attribute, $value, $fail) use ($binaryId) {
                    $emailHash = hash('sha256', strtolower($value), true);
                    $exists = DB::table('students')
                        ->where('email_hash', $emailHash)
                        ->where('id', '!=', $binaryId)
                        ->exists();
                    if ($exists) {
                        $fail('Este e-mail já está vinculado a outro aluno.');
                    }
                }
            ],
            'phone' => [
                'sometimes',
                'string',
                'size:11',
                function ($attribute, $value, $fail) use ($binaryId) {
                    if (!Validators::validatePhoneNumber($value)) {
                        $fail('Número de celular inválido. Certifique-se de que o número contenha 11 dígitos, começando com um DDD válido seguido de um número celular iniciado por 9.');
                        return;
                    }
                    if ($value) {
                        $phoneHash = hash('sha256', $value, true);
                        $exists = DB::table('students')
                            ->where('phone_hash', $phoneHash)
                            ->where('id', '!=', $binaryId)
                            ->exists();
                        if ($exists) {
                            $fail('Este telefone já está vinculado a outro aluno.');
                        }
                    }
                }
            ],
            'course' => [
                'sometimes',
                function ($attribute, $value, $fail) {
                    if (!Validators::validateCourseName($value)) {
                        $fail('O curso informado não foi reconhecido.');
                    }
                }
            ],
            'term' => [
                'sometimes',
                Rule::in(['Annual', 'Semester'])
            ],
            'period' => [
                'sometimes',
                'integer',
                'between:1,20'
            ],
        ];
    }

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
}
