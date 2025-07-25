<?php

namespace App\Http\Requests\Posts;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PostStoreRequest extends FormRequest
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
            'user_id' => [
                'required',
                'integer',
                'exists:users,id'
            ],
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id'
            ],
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'content' => [
                'required',
                'string',
                'min:10',
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('posts', 'slug'),
            ],
        ];
    }

    /**
     * Summary of messages
     * @return array{content.min: string, content.required: string, slug.required: string, slug.unique: string, title.max: string, title.required: string}
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'O autor é obrigatório.',
            'user_id.exists' => 'O usuário selecionado não existe.',
            'category_id.required' => 'A categoria é obrigatória.',
            'category_id.exists' => 'A categoria selecionada não existe.',
            'title.required' => 'O título é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'content.required' => 'O conteúdo é obrigatório.',
            'content.min' => 'O conteúdo deve ter pelo menos 10 caracteres.',
            'slug.required' => 'O slug é obrigatório.',
            'slug.unique' => 'Este slug já está em uso.',
        ];
    }

    /**
     * Summary of prepareForValidation
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if (auth()->check()) {
            $this->merge([
                'user_id' => auth()->id(),
            ]);
        }
    }
}
