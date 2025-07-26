<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;

class ImageRequest extends FormRequest
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
            'images' => [
                'required',
                'array',
                'min:1',
            ],
            'images.*' => [
                'required',
                // 'mimes:jpg,jpeg,png,svg,gif,webp',
                // 'min:1',
                // 'max:5120'
                File::types(['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp'])
                    ->min('1kb')
                    ->max('5mb')
            ]
        ];
    }

    /**
     * Summary of messages
     * @return array{images.*.file: string, images.*.image: string, images.*.max: string, images.*.mimes: string, images.*.min: string, images.*.required: string, images.array: string, images.min: string, images.required: string}
     */
    public function messages(): array
    {
        return [
            'images.required' => 'É necessário enviar pelo menos uma imagem.',
            'images.array' => 'O formato do envio de imagens é inválido.',
            'images.min' => 'Você deve enviar no mínimo uma imagem.',

            'images.*.required' => 'O campo imagem é obrigatório.',
            'images.*.file' => 'O arquivo enviado não é válido.',

            // Mensagens específicas para File validation
            'images.*.mimetypes' => 'O arquivo deve ser uma imagem nos formatos: jpg, jpeg, png, svg, gif ou webp.',
            'images.*.min' => 'O tamanho mínimo da imagem deve ser 1KB.',
            'images.*.max' => 'O tamanho máximo permitido para cada imagem é 5MB.'
        ];
    }

    /**
     * Custom attribute names
     */
    public function attributes(): array
    {
        return [
            'images' => 'conjunto de imagens',
            'images.*' => 'imagem'
        ];
    }
}
