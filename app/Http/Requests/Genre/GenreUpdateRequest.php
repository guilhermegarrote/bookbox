<?php

namespace App\Http\Requests\Genre;

use App\Helpers\Utils;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class GenreUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->input('name', '')),
            'color_hex' => $this->has('color_hex') && $this->input('color_hex') !== null
                ? strtoupper(ltrim(trim($this->input('color_hex')), '#'))
                : null,
        ]);
    }

    public function rules(): array
    {
        $binaryId = Utils::convertUuidToBinary($this->route('genre'));

        return [
            'name' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[\pL\s.\'-]+$/u',
                function ($attribute, $value, $fail) use ($binaryId) {
                    $exists = DB::table('genres')
                        ->where('name', $value)
                        ->where('id', '!=', $binaryId)
                        ->exists();
                    if ($exists) {
                        $fail('Este gênero já está cadastrado.');
                    }
                }
            ],
            'color_hex' => [
                'sometimes',
                'nullable',
                'string',
                'regex:/^[0-9A-Fa-f]{6}$/',
                function ($attribute, $value, $fail) use ($binaryId) {
                    $exists = DB::table('genres')
                        ->where('color_hex', $value)
                        ->where('id', '!=', $binaryId)
                        ->exists();
                    if ($exists) {
                        $fail('Esta cor já está relacionada a outro gênero.');
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'O nome deve ser um texto.',
            'name.max' => 'O nome não pode ter mais que 100 caracteres.',
            'name.regex' => 'O nome contém caracteres inválidos.',

            'color_hex.string' => 'A cor deve ser um texto.',
            'color_hex.regex' => 'A cor deve estar no formato hexadecimal de 6 caracteres, por exemplo: A1B2C3.',
        ];
    }
}
