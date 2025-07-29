<?php

namespace App\Http\Requests\Student;

use App\Helpers\Validators;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentStoreRequest extends FormRequest
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
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    if (!Validators::validateFullName($value)) {
                        $fail('O nome deve conter entre 3 e 100 caracteres, com pelo menos nome e sobrenome. Apenas letras, espaços, hífens e apóstrofos são permitidos.');
                    }
                }
            ],
            'cpf' => [
                'required',
                'string',
                'size:11',
                function ($attribute, $value, $fail) {
                    if (!Validators::validateCpf($value)) {
                        $fail('O CPF é inválido.');
                        return;
                    }
                    $cpfHash = hash('sha256', $value, true);
                    $exists = DB::table('students')->where('cpf_hash', $cpfHash)->exists();
                    if ($exists) {
                        $fail('Este CPF já está vinculado a outro aluno.');
                    }
                }
            ],
            'email' => [
                'required',
                'email',
                'max:100',
                function ($attribute, $value, $fail) {
                    $emailHash = hash('sha256', strtolower($value), true);
                    $exists = DB::table('students')->where('email_hash', $emailHash)->exists();
                    if ($exists) {
                        $fail('Este e-mail já está vinculado a outro aluno.');
                    }
                }
            ],
            'phone' => [
                'nullable',
                'string',
                'size:11',
                function ($attribute, $value, $fail) {
                    if (!Validators::validatePhoneNumber($value)) {
                        $fail('Número de celular inválido. Certifique-se de que o número contenha 11 dígitos, começando com um DDD válido seguido de um número celular iniciado por 9.');
                        return;
                    }
                    if ($value) {
                        $phoneHash = hash('sha256', $value, true);
                        $exists = DB::table('students')->where('phone_hash', $phoneHash)->exists();
                        if ($exists) {
                            $fail('Este telefone já está vinculado a outro aluno.');
                        }
                    }
                }
            ],
            'course' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Validators::validateCourseName($value)) {
                        $fail('O curso informado não foi reconhecido.');
                    }
                }
            ],
            'term' => [
                'required',
                Rule::in(['Annual', 'Semester'])
            ],
            'period' => [
                'required',
                'integer',
                'between:1,20'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto.',
            'name.max' => 'O nome não pode ter mais que 100 caracteres.',

            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.string' => 'O CPF deve ser uma string.',
            'cpf.size' => 'O CPF deve conter exatamente 11 dígitos.',

            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O formato do e-mail é inválido.',
            'email.max' => 'O e-mail não pode ter mais que 100 caracteres.',

            'phone.string' => 'O telefone deve ser uma string.',
            'phone.size' => 'O telefone deve conter 11 dígitos, incluindo DDD. Ex: 11999998888.',

            'course.required' => 'O curso é obrigatório.',

            'term.required' => 'O regime é obrigatório.',
            'term.in' => 'Regime inválido. Os valores aceitos são: Anual ou Semestral.',

            'period.required' => 'O período é obrigatório.',
            'period.integer' => 'O período deve ser um número inteiro.',
            'period.min' => 'O período informado deve ser um número entre 1 e 20.',
            'period.max' => 'O período informado deve ser um número entre 1 e 20.',
        ];
    }
}
