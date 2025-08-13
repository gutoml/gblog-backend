<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\PostHighline;
use Illuminate\Support\Collection;
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

    public function test_list_posts_highline(): void
    {
        $limit = 4;

        PostHighline::truncate();

        $postsHighline = PostHighline::factory()
            ->count($limit)
            ->sequence(fn ($sequence) => ['order' => $sequence->index + 1])
            ->create();

        $response = $this->getJson(route('post-highline.index'));

        $response->assertOk();
        $response->assertJsonCount($limit);

        // Ordena pelo campo 'order' para comparar corretamente
        $postsHighline = $postsHighline->sortBy('order')->values();

        $postsHighline->each(function ($post, $index) use ($response) {
            $response->assertJsonPath("{$index}.post_id", $post->post_id);
            $response->assertJsonPath("{$index}.order", $post->order);

            $this->assertDatabaseHas('post_highlines', [
                'post_id' => $post->post_id,
                'order' => $post->order,
            ]);
        });
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

    public function test_delete_post_highline()
    {
        $this->actingAs($this->user);

        $postHighline = PostHighline::factory()->create();

        $response = $this->deleteJson(route('post-highline.destroy', $postHighline->id));

        $response->assertNoContent();
        $this->assertDatabaseMissing('post_highlines', [
            'post_id' => $postHighline->id,
            'order' => $postHighline->order,
        ]);
    }
}
