<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Helpers\Validators;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

/**
 * Handles validation logic for creating a new user.
 *
 * This request ensures that the provided name, email, and password
 * comply with structural, uniqueness, and formatting rules before
 * being persisted to the database.
 *
 * @method mixed input(string $key, mixed $default = null) Retrieve an input item from the request.
 * @method void merge(array $input) Merge new input into the request's data.
 */
class UserStoreRequest extends FormRequest
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
     * Define validation rules for the user creation request.
     *
     * @return array<string, mixed> validation rules for user input fields
     */
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
                },
            ],
            'email' => [
                'required',
                'email',
                'max:320',
                function ($attribute, $value, $fail) {
                    if (!Validators::validateEmail($value)) {
                        $fail('Este e-mail é inválido.');

                        return;
                    }

                    $emailHash = hash('sha256', strtolower($value), true);
                    $exists = DB::table('users')->where('email_hash', $emailHash)->exists();

                    if ($exists) {
                        $fail('Este e-mail já está vinculado a outro usuário.');
                    }
                },
            ],
            'password' => [
                'required',
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
     * Custom error messages for validation failures.
     *
     * @return array<string, string> the validation messages mapped to rules
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto.',
            'name.max' => 'O nome não pode ter mais que 100 caracteres.',

            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O formato do e-mail é inválido.',
            'email.max' => 'O e-mail não pode ter mais que 320 caracteres.',

            'password.required' => 'A senha é obrigatória.',
            'password.string' => 'A senha deve ser uma string.',
            'password.min' => 'A senha deve conter no mínimo 8 caracteres.',
            'password.max' => 'A senha pode ter no máximo 16 caracteres.',
            'password.confirmed' => 'A confirmação de senha deve ser idêntica à senha informada.',
        ];
    }

    /**
     * Prepare input data before applying validation rules.
     *
     * This method trims text fields and ensures consistent formatting
     * for email and password inputs to prevent false negatives during validation.
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
