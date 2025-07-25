<?php

namespace App\Http\Requests\Posts;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PostUpdateRequest extends FormRequest
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
                'sometimes',
                'integer',
                'exists:users,id'
            ],
            'category_id' => [
                'sometimes',
                'integer',
                'exists:categories,id'
            ],
            'title' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'content' => [
                'sometimes',
                'string',
                'min:10',
            ],
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('posts', 'slug')->ignore($this->post?->id),
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
            'user_id.sometimes' => 'O autor é obrigatório.',
            'user_id.exists' => 'O usuário selecionado não existe.',
            'category_id.sometimes' => 'A categoria é obrigatória.',
            'category_id.exists' => 'A categoria selecionada não existe.',
            'title.sometimes' => 'O :attribute é obrigatório.',
            'title.max' => 'O :attribute não pode ter mais de 255 caracteres.',
            'content.sometimes' => 'O :attribute é obrigatório.',
            'content.min' => 'O :attribute deve ter pelo menos 10 caracteres.',
            'slug.sometimes' => 'O :attribute é obrigatório.',
            'slug.unique' => 'Este :attribute já está em uso.',
        ];
    }

    /**
     * Summary of attributes
     * @return array{content: string, slug: string, title: string}
     */
    public function attributes(): array
    {
        return [
            'title' => 'título',
            'content' => 'conteúdo',
            'slug' => 'slug',
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
