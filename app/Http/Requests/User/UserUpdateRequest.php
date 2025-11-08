<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Helpers\Utils;
use App\Helpers\Validators;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

/**
 * Handles validation logic for updating an existing user.
 *
 * This request ensures that updated fields such as name, email, and password
 * meet the defined validation standards and maintain data integrity.
 *
 * @method mixed input(string $key, mixed $default = null) Retrieve an input item from the request.
 * @method void merge(array $input) Merge new input into the request's data.
 * @method mixed route(string|null $key = null, mixed $default = null) Retrieve a route parameter value.
 */
class UserUpdateRequest extends FormRequest
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
     * Define validation rules for updating a user.
     *
     * The method validates optional fields when present in the request payload.
     *
     * @return array<string, mixed> validation rules for user input fields
     */
    public function rules(): array
    {
        $binaryId = Utils::convertUuidToBinary($this->route('user'));

        return [
            'name' => [
                'sometimes',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    if (!Validators::validateFullName($value)) {
                        $fail('O nome deve conter entre 3 e 100 caracteres, com pelo menos nome e sobrenome. Apenas letras, espaços, hífens e apóstrofos são permitidos.');
                    }
                },
            ],
            'email' => [
                'sometimes',
                'email',
                'max:320',
                function ($attribute, $value, $fail) use ($binaryId) {
                    if (!Validators::validateEmail($value)) {
                        $fail('Este e-mail é inválido.');

                        return;
                    }

                    $emailHash = hash('sha256', strtolower($value), true);
                    $exists = DB::table('users')
                        ->where('email_hash', $emailHash)
                        ->where('id', '!=', $binaryId)
                        ->exists()
                    ;

                    if ($exists) {
                        $fail('Este e-mail já está vinculado a outro usuário.');
                    }
                },
            ],
            'password' => [
                'sometimes',
                'string',
                'min:8',
                'max:16',
                'confirmed',
                function ($attribute, $value, $fail) {
                    if (!Validators::validatePasswordStructure($value)) {
                        $fail('A senha deve ter de 8 a 16 caracteres, conter pelo menos 1 letra maiúscula, 1 minúscula, 1 número e 1 caractere especial.');
                    }
                },
            ],
        ];
    }

    /**
     * Custom validation messages for the defined rules.
     *
     * @return array<string, string> error messages for validation failures
     */
    public function messages(): array
    {
        return [
            'name.string' => 'O nome deve ser um texto.',
            'name.max' => 'O nome não pode ter mais que 100 caracteres.',

            'email.email' => 'O formato do e-mail é inválido.',
            'email.max' => 'O e-mail não pode ter mais que 320 caracteres.',

            'password.string' => 'A senha deve ser uma string.',
            'password.min' => 'A senha deve conter no mínimo 8 caracteres.',
            'password.max' => 'A senha pode ter no máximo 16 caracteres.',
            'password.confirmed' => 'A confirmação de senha deve ser idêntica à senha informada.',
        ];
    }

    /**
     * Prepare input data before applying validation rules.
     *
     * Trims all string inputs to remove unnecessary whitespace
     * and ensures clean formatting for consistent validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->input('name', '')),
            'email' => trim($this->input('email', '')),
            'password' => trim($this->input('password', '')),
            'password_confirmation' => trim($this->input('password_confirmation', '')),
        ]);
    }
}
