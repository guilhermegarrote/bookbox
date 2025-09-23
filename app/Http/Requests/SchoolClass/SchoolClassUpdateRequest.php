<?php

namespace App\Http\Requests\SchoolClass;

use App\Helpers\Validators;
use App\Rules\ValidTermInterval;
use App\Models\SchoolClass;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Helpers\Utils;

class SchoolClassUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'course' => trim($this->input('course', '')),
            'term' => trim($this->input('term', '')),
            'start_date' => trim($this->input('start_date', '')),
            'end_date' => trim($this->input('end_date', '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'course' => [
                'sometimes',
                function ($attribute, $value, $fail) {
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
}
