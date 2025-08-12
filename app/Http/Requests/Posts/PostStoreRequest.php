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
            'image_id' => [
                'required',
                Rule::exists('images', 'id')
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
            'related_posts' => [
                'nullable',
                'array',
                '*' => [
                    'required',
                    'string',
                    Rule::exists('posts', 'id')
                ]
            ]
        ];
    }

    /**
     * Summary of messages
     * @return array{
     * content.min: string,
     * content.required: string,
     * slug.required: string,
     * slug.unique: string,
     * title.max: string,
     * title.required: string,
     * related_posts: string,
     * }
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'O autor é obrigatório.',
            'user_id.exists' => 'O usuário selecionado não existe.',
            'category_id.required' => 'A :attribute é obrigatória.',
            'category_id.exists' => 'A :attribute selecionada não existe.',
            'image_id.required' => 'Escolha uma :attribute.',
            'image_id.exists' => 'Essa :attribute não existe.',
            'title.required' => 'O :attribute é obrigatório.',
            'title.max' => 'O :attribute não pode ter mais de 255 caracteres.',
            'content.required' => 'O :attribute é obrigatório.',
            'content.min' => 'O :attribute deve ter pelo menos 10 caracteres.',
            'slug.required' => 'O :attribute é obrigatório.',
            'slug.unique' => 'Este :attribute já está em uso.',
            'related_posts.array' => 'O campo de :attribute deve ser uma lista.',
            'related_posts.*.required' => 'O campo de :attribute não pode ser vazio.',
            'related_posts.*.string' => 'O campo de :attribute não é válido.',
            'related_posts.*.exists' => 'O campo de :attribute não é válido.',
        ];
    }

    /**
     * Summary of attributes
     * @return array{category_id: \App\Models\Category, image_id: \App\Models\Image, content: string, slug: string, title: string, related_posts: string}
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'categoria',
            'image_id' => 'imagem',
            'title' => 'título',
            'content' => 'conteúdo',
            'slug' => 'slug',
            'related_posts' => 'postagens relacionadas'
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
