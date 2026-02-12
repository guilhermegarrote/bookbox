<?php

declare(strict_types=1);

namespace App\Http\Requests\SchoolClass;

use App\Helpers\Validators;
use App\Rules\ValidTermInterval;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Handles validation for updating an existing school class.
 *
 * Prepares and sanitizes input data and validates optional fields:
 * course, term, start_date, and end_date.
 *
 * @method mixed input(string $key, mixed $default = null) Retrieve an input item from the request.
 * @method bool has(string $key) Determine if the request contains a given input key.
 * @method void merge(array $input) Merge new input into the request's data.
 * @method array all(?array $keys = null) Retrieve all input data or a subset.
 * @method array only(array|string $keys) Retrieve only a subset of input data.
 */
class SchoolClassUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool true if authorized
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
                'sometimes',
                function ($attribute, $value, $fail): void {
                    if (!Validators::validateCourseName($value)) {
                        $fail('O curso informado não foi reconhecido.');
                    }
                },
            ],
            'term' => ['sometimes', Rule::in(['Annual', 'Semester'])],
            'start_date' => ['sometimes', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
            'end_date' => [
                'sometimes',
                'date',
                'date_format:Y-m-d',
                'after:start_date',
                new ValidTermInterval($this->start_date, $this->term),
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
            'term.in' => 'Regime inválido. Os valores aceitos são: Anual ou Semestral.',

            'start_date.date' => 'A data de início fornecida não é válida.',
            'start_date.date_format' => 'A data de início deve estar no formato AAAA-MM-DD.',
            'start_date.before_or_equal' => 'A data de início não pode ser posterior ao dia de hoje.',

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
