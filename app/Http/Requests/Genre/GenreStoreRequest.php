<?php

declare(strict_types=1);

namespace App\Http\Requests\Genre;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request responsible for validating data when creating a new genre.
 *
 * This request ensures that both the genre name and color code
 * are unique and properly formatted before being persisted to the database.
 *
 * @method array only(array|string $keys) Retrieve only a subset of input data.
 * @method array all(?array $keys = null) Retrieve all input data or a subset.
 * @method bool has(array|string $key) Determine if the request contains a given input key.
 * @method mixed input(string $key, mixed $default = null) Retrieve an input item from the request.
 * @method void merge(array $input) Merge new input into the request's data.
 */
class GenreStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool true if the user is authorized to perform this request
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed> validation rules for the incoming request data
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:genres,name',
                'regex:/^[\pL\s.\'-]+$/u',
            ],
            'color_hex' => [
                'required',
                'string',
                'unique:genres,color_hex',
                'regex:/^[0-9A-Fa-f]{6}$/',
            ],
        ];
    }

    /**
     * Get the custom messages for validation errors.
     *
     * @return array<string, string> custom validation messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome do gênero é obrigatório.',
            'name.string' => 'O nome do gênero deve ser um texto.',
            'name.max' => 'O nome do gênero não pode ter mais que 100 caracteres.',
            'name.unique' => 'Já existe um gênero com este nome.',
            'name.regex' => 'O nome do gênero contém caracteres inválidos.',

            'color_hex.required' => 'A cor é obrigatória.',
            'color_hex.string' => 'A cor deve ser um texto.',
            'color_hex.unique' => 'Já existe um gênero com esta cor.',
            'color_hex.regex' => 'A cor deve ser um código hexadecimal válido de 6 caracteres, ex: FF0000.',
        ];
    }

    /**
     * Prepare the data for validation by sanitizing and normalizing inputs.
     *
     * This method trims the name field and converts the color hex code
     * to uppercase while removing the leading "#" character if present.
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
