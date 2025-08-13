<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\PostHighline;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostHighline>
 */
class PostHighlineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'order' => fake()->unique()->numberBetween(1, 1000),
        ];
    }
}
