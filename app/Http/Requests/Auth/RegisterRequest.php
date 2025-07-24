<?php

namespace App\Http\Requests\Auth;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZÀ-ú]+\s[a-zA-ZÀ-ú]+(\s[a-zA-ZÀ-ú]+)*$/'
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email'
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->max(32)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
        ];
    }

    /**
     * Mensagens de erro personalizadas
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome completo é obrigatório.',
            'name.regex' => 'Por favor, insira pelo menos nome e sobrenome.',
            'email.unique' => 'Este e-mail já está em uso.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.confirmed' => 'A confirmação de senha não corresponde.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.max' => 'A senha deve ter no máximo 32 caracteres.',
            'password.letters' => 'A senha deve conter letras.',
            'password.mixedCase' => 'A senha deve conter letras maiúsculas e minúsculas.',
            'password.numbers' => 'A senha deve conter números.',
            'password.symbols' => 'A senha deve conter símbolos especiais.',
            'password.uncompromised' => 'Esta senha foi comprometida em vazamentos de dados. Por favor, escolha outra senha.',
        ];
    }

    /**
     * Preparar os dados para validação
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'name' => trim(preg_replace('/\s+/', ' ', $this->name)), // Remove espaços extras
        ]);
    }
}
