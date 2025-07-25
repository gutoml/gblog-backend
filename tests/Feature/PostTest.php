<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_invalid_data(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $this->postJson(route('posts.store'), [
            'category_id' => null,
            'title' => '',
            'content' => '',
            'slug' => '',
        ])->assertStatus(422)
        ->assertJsonValidationErrors(['category_id', 'title', 'content', 'slug']);
    }

    public function test_valid_data()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $category = \App\Models\Category::factory()->create();

        $payload = [
            'category_id' => $category->id,
            'title' => 'Valid Post Title',
            'content' => 'This is the content of the post.',
            'slug' => 'valid-post-title',
        ];

        $response = $this->postJson(route('posts.store'), $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'category_id' => $category->id,
                'title' => 'Valid Post Title',
                'content' => 'This is the content of the post.',
                'slug' => 'valid-post-title',
            ]);

        $this->assertDatabaseHas('posts', [
            'category_id' => $category->id,
            'title' => 'Valid Post Title',
            'content' => 'This is the content of the post.',
            'slug' => 'valid-post-title',
        ]);
    }

    /**
     * Summary of test_update_with_valid_data
     * @return void
     */
    public function test_update_with_valid_data()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $category = \App\Models\Category::factory()->create();
        $post = \App\Models\Post::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $updatePayload = [
            'category_id' => $category->id,
            'title' => 'Updated Title',
            'content' => 'Updated content of the post.',
            'slug' => 'updated-title',
        ];

        $response = $this->putJson(route('posts.update', $post->id), $updatePayload);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'category_id' => $category->id,
                'title' => 'Updated Title',
                'content' => 'Updated content of the post.',
                'slug' => 'updated-title',
            ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'category_id' => $category->id,
            'title' => 'Updated Title',
            'content' => 'Updated content of the post.',
            'slug' => 'updated-title',
        ]);
    }

    /**
     * Summary of test_update_with_invalid_data
     * @return void
     */
    public function test_update_with_invalid_data()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $category = \App\Models\Category::factory()->create();
        $post = \App\Models\Post::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $invalidPayload = [
            'category_id' => null,
            'title' => '',
            'content' => '',
            'slug' => '',
        ];

        $response = $this->putJson(route('posts.update', $post->id), $invalidPayload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category_id', 'title', 'content', 'slug']);
    }

    public function test_delete_data()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        $category = \App\Models\Category::factory()->create();
        $post = \App\Models\Post::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->deleteJson(route('posts.destroy', $post->id));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }
}
