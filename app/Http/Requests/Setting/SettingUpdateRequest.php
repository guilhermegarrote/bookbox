<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class SettingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'value' => $this->input('value'), 
        ]);
    }

    public function rules(): array
    {
        return [
            'value' => [
                'sometimes',
                'integer'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'value.integer' => 'O valor deve ser um número inteiro.',
        ];
    }
}
