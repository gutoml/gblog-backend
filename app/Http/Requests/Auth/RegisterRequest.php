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
            'name.required' => 'O :attribute é obrigatório.',
            'name.regex' => 'Por favor, insira seu :attribute.',
            'email.required' => 'O campo :attribute é obrigatório.',
            'email.unique' => 'Este :attribute já está em uso.',
            'password.required' => 'O campo :attribute é obrigatório.',
            'password.confirmed' => 'A confirmação de :attribute não corresponde.',
            'password.min' => 'A :attribute deve ter no mínimo 8 caracteres.',
            'password.max' => 'A :attribute deve ter no máximo 32 caracteres.',
            'password.letters' => 'A :attribute deve conter letras.',
            'password.mixedCase' => 'A :attribute deve conter letras maiúsculas e minúsculas.',
            'password.numbers' => 'A :attribute deve conter números.',
            'password.symbols' => 'A :attribute deve conter símbolos especiais.',
            'password.uncompromised' => 'Esta :attribute foi comprometida em vazamentos de dados. Por favor, escolha outra :attribute.',
        ];
    }

    /**
     * Summary of attributes
     * @return array{email: string, name: string, password: string}
     */
    public function attributes(): array
    {
        return [
            'name' => 'nome completo',
            'email' => 'e-mail',
            'password' => 'senha',
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
