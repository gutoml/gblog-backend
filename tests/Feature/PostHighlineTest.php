<?php

namespace Tests\Feature;

use Illuminate\Support\Collection;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostHighlineTest extends TestCase
{
    use RefreshDatabase;

    private Collection $posts;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->posts = Post::factory()->count(7)->create();
        $this->user = User::factory()->create();
    }

    /**
     * A basic feature test example.
     */
    public function test_store_valid_data(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('post-highline.store'), [
            'posts' => $this->posts->pluck('id')->toArray(),
        ]);

        $response->assertStatus(201);

        $this->posts->each(function($post, $index) use ($response) {
            $response->assertJsonPath("{$index}.post_id", $post->id);
            $response->assertJsonPath("{$index}.order", $index + 1);

            $this->assertDatabaseHas('post_highlines', [
                'post_id' => $post->id,
                'order' => $index + 1,
            ]);
        });
    }

    public function test_store_invalid_data(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('post-highline.store'), [
            'posts' => [null, null],
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['posts.0', 'posts.1']);
    }

    public function test_update_valid_data(): void
    {
        $this->actingAs($this->user);

        $response = $this->putJson(route('post-highline.update'), [
            'posts' => $this->posts->pluck('id')->toArray(),
        ]);

        $this->posts->each(function($post, $index) use ($response) {
            $response->assertJsonPath("{$index}.post_id", $post->id);
            $response->assertJsonPath("{$index}.order", $index + 1);

            $this->assertDatabaseHas('post_highlines', [
                'post_id' => $post->id,
                'order' => $index + 1,
            ]);
        });
    }

    public function test_update_invalid_data(): void
    {
        $this->actingAs($this->user);

        $response = $this->putJson(route('post-highline.update'), [
            'posts' => [null, null],
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['posts.0', 'posts.1']);
    }
}
