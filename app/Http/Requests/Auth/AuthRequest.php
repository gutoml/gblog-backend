<?php

namespace App\Http\Requests\Auth;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
            'email' => [
                'required',
                'email'
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:32',
                Password::min(8)
                    ->max(32)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ];
    }

    /**
     * Summary of messages
     * @return array{email.email: string, email.required: string, password.letters: string, password.max: string, password.min: string, password.mixedCase: string, password.numbers: string, password.required: string, password.symbols: string}
     */
    public function messages(): array
    {
        return [
            'email.required' => 'O :attribute é obrigatório.',
            'email.email' => 'Informe um :attribute válido.',
            'password.required' => 'A :attribute é obrigatória.',
            'password.min' => 'A :attribute deve ter no mínimo 8 caracteres.',
            'password.max' => 'A :attribute deve ter no máximo 32 caracteres.',
            'password.letters' => 'A :attribute deve conter letras.',
            'password.mixedCase' => 'A :attribute deve conter letras maiúsculas e minúsculas.',
            'password.numbers' => 'A :attribute deve conter números.',
            'password.symbols' => 'A :attribute deve conter símbolos especiais.',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'e-mail',
            'password' => 'senha',
        ];
    }
}
