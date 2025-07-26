<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Cria um arquivo de imagem fake
        $file = UploadedFile::fake()->image(
            $this->faker->word().'.'.$this->faker->randomElement(['jpg', 'jpeg', 'png', 'gif']),
            800, // largura
            600 // altura
        );

        // Armazena o arquivo
        $path = Storage::disk('public')->put(
            'images',
            $file,
        );

        return [
            'name' => $file->getClientOriginalName(),
            'url' => Storage::url($path),
        ];
    }

    /**
     * Configura a factory para usar um disco específico
     */
    public function withDisk(string $disk): static
    {
        return $this->state(function (array $attributes) use ($disk) {
            return ['disk' => $disk];
        });
    }

    /**
     * Configura a factory para usar um diretório específico
     */
    public function withDirectory(string $directory): static
    {
        return $this->state(function (array $attributes) use ($directory) {
            return ['directory' => $directory];
        });
    }
}
