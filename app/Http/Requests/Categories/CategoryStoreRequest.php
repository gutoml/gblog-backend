<?php

namespace App\Http\Requests\Categories;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CategoryStoreRequest extends FormRequest
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
                'max:255'
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($this->category?->id),
            ],
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O campo do :attribute é obrigatório.',
            'name.string' => 'O campo do :attribute deve ser texto.',
            'name.max' => 'O campo do :attribute deve ter no máximo 255 caracteres.',
            'description.string' => 'O campo da :attribute deve ser texto.',
            'description.max' => 'O campo da :attribute deve ter no máximo 1000 caracteres.',
            'slug.required' => 'O campo do :attribute é obrigatório.',
            'slug.string' => 'O campo do :attribute deve ser texto.',
            'slug.max' => 'O campo do :attribute deve ter no máximo 255 caracteres.',
            'slug.unique' => 'O :attribute já está em uso.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'description' => 'descrição',
            'slug' => 'slug',
        ];
    }
}
