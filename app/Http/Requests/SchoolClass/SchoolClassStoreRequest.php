<?php

declare(strict_types=1);

namespace App\Http\Requests\SchoolClass;

use App\Helpers\Validators;
use App\Models\SchoolClass;
use App\Rules\ValidTermInterval;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Handles validation for creating a new school class.
 *
 * Prepares input data, validates course, term, start and end dates,
 * and ensures uniqueness of class data.
 *
 * @method mixed input(string $key, mixed $default = null) Retrieve an input item from the request.
 * @method void merge(array $input) Merge new input into the request's data.
 * @method array only(array|string $keys) Retrieve only a subset of input data.
 */
class SchoolClassStoreRequest extends FormRequest
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
            'course' => [
                'required',
                function ($attribute, $value, $fail): void {
                    if (!Validators::validateCourseName($value)) {
                        $fail('O curso informado não foi reconhecido.');
                    }
                },
            ],
            'term' => ['required', Rule::in(['Annual', 'Semester'])],
            'start_date' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
            'end_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after:start_date',
                new ValidTermInterval($this->start_date, $this->term),
            ],
        ];
    }

    /**
     * Add additional validation rules after the initial rules are applied.
     *
     * Ensures that the combination of course, regime, start_date, and end_date
     * is unique in the database.
     *
     * @param \Illuminate\Validation\Validator $validator
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $data = $this->only(['course', 'regime', 'start_date', 'end_date']);

            if (SchoolClass::where($data)->exists()) {
                $validator->errors()->add('course', 'Já existe uma turma cadastrada com esses dados.');
            }
        });
    }

    /**
     * Get custom error messages for validation failures.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'course.required' => 'O curso é obrigatório.',

            'term.required' => 'O regime é obrigatório.',
            'term.in' => 'Regime inválido. Os valores aceitos são: Anual ou Semestral.',

            'start_date.required' => 'A data de início é obrigatória.',
            'start_date.date' => 'A data de início fornecida não é válida.',
            'start_date.date_format' => 'A data de início deve estar no formato AAAA-MM-DD.',
            'start_date.before_or_equal' => 'A data de início não pode ser posterior ao dia de hoje.',

            'end_date.required' => 'A data de fim é obrigatória.',
            'end_date.date' => 'A data de fim fornecida não é válida.',
            'end_date.date_format' => 'A data de fim deve estar no formato AAAA-MM-DD.',
            'end_date.after' => 'A data de fim não pode ser anterior à data de início.',
        ];
    }

    /**
     * Prepare input data before validation.
     *
     * Trims whitespace from all input fields and formats date fields.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'course' => trim($this->input('course', '')),
            'term' => trim($this->input('term', '')),

            'start_date' => $this->input('start_date')
                ? Carbon::createFromFormat('d/m/Y', $this->input('start_date'))->format('Y-m-d')
                : null,

            'end_date' => $this->input('end_date')
                ? Carbon::createFromFormat('d/m/Y', $this->input('end_date'))->format('Y-m-d')
                : null,
        ]);
    }
}
