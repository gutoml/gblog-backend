<?php

namespace App\Http\Requests\PostHighlines;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostHighlineStoreRequest extends FormRequest
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
            'posts' => [
                'required',
                'array',
            ],
            'posts.*' => [
                'required',
                Rule::exists('posts', 'id')
            ]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'posts.required' => 'A lista de posts é obrigatória.',
            'posts.array' => 'O campo posts deve ser uma lista.',
            'posts.*.required' => 'Cada item da lista de posts é obrigatório.',
            'posts.*.exists' => 'Um ou mais posts selecionados não existem.'
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        return [
            'posts' => collect($validated['posts'])->map(function ($postId, $index) {
                return [
                    'post_id' => $postId,
                    'order' => $index + 1
                ];
            })->values()->toArray()
        ];
    }
}
