<?php

declare(strict_types=1);

namespace App\Http\Requests\Genre;

use App\Helpers\Utils;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

/**
 * Handles validation for updating an existing genre.
 *
 * Includes preprocessing of input values and validation to ensure
 * that both the name and color code are unique across genres.
 *
 * @method bool has(array|string $key) Determine if the request contains a given input key.
 * @method mixed input(string $key, mixed $default = null) Retrieve an input item from the request.
 * @method void merge(array $input) Merge new input into the request's data.
 * @method mixed route(string $key = null, $default = null)
 */
class GenreUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
        $binaryId = Utils::convertUuidToBinary($this->route('genre'));

        return [
            'name' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[\pL\s.\'-]+$/u',
                function ($attribute, $value, $fail) use ($binaryId): void {
                    $exists = DB::table('genres')
                        ->where('name', $value)
                        ->where('id', '!=', $binaryId)
                        ->exists()
                    ;

                    if ($exists) {
                        $fail('Este gênero já está cadastrado.');
                    }
                },
            ],
            'color_hex' => [
                'sometimes',
                'nullable',
                'string',
                'regex:/^[0-9A-Fa-f]{6}$/',
                function ($attribute, $value, $fail) use ($binaryId): void {
                    $exists = DB::table('genres')
                        ->where('color_hex', $value)
                        ->where('id', '!=', $binaryId)
                        ->exists()
                    ;

                    if ($exists) {
                        $fail('Esta cor já está relacionada a outro gênero.');
                    }
                },
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
            'name.string' => 'O nome deve ser um texto.',
            'name.max' => 'O nome não pode ter mais que 100 caracteres.',
            'name.regex' => 'O nome contém caracteres inválidos.',

            'color_hex.string' => 'A cor deve ser um texto.',
            'color_hex.regex' => 'A cor deve estar no formato hexadecimal de 6 caracteres, por exemplo: A1B2C3.',
        ];
    }

    /**
     * Prepare input data before validation.
     *
     * Trims whitespace and normalizes the color code to uppercase
     * without the "#" prefix.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->input('name', '')),
            'color_hex' => $this->has('color_hex') && $this->input('color_hex') !== null
                ? strtoupper(ltrim(trim($this->input('color_hex')), '#'))
                : null,
        ]);
    }
}
