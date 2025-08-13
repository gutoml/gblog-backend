<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Image;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Category $category1;
    private Category $category2;

    private Image $image1;
    private Image $image2;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->user = User::factory()->create();
        $this->category1 = Category::factory()->create();
        $this->category2 = Category::factory()->create();
        $this->image1 = Image::factory()->create();
        $this->image2 = Image::factory()->create();
    }

    /**
     * A basic feature test example.
     */
    public function test_invalid_data(): void
    {
        $this->actingAs($this->user);

        $this->postJson(route('posts.store'), [
            'category_id' => null,
            'image_id' => null,
            'title' => '',
            'content' => '',
            'slug' => '',
        ])->assertStatus(422)
        ->assertJsonValidationErrors(['category_id', 'image_id', 'title', 'content', 'slug']);
    }

    public function test_valid_data()
    {
        $this->actingAs($this->user);

        $relatedPost = Post::factory()->create();

        $response = $this->postJson(route('posts.store'), [
            'category_id' => $this->category1->id,
            'image_id' => $this->image1->id,
            'title' => 'Valid Post Title',
            'content' => 'This is the content of the post.',
            'slug' => 'valid-post-title',
            'related_posts' => [
                $relatedPost->id
            ]
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'user_id' => $this->user->id,
                'category_id' => $this->category1->id,
                'title' => 'Valid Post Title',
                'content' => 'This is the content of the post.',
                'slug' => 'valid-post-title',
            ])
            ->assertJsonPath('related_posts.0.id', $relatedPost->id)
            ->assertJsonPath('related_posts.0.user_id', $relatedPost->user_id)
            ->assertJsonPath('related_posts.0.category_id', $relatedPost->category_id)
            ->assertJsonPath('related_posts.0.title', $relatedPost->title)
            ->assertJsonPath('related_posts.0.content', $relatedPost->content)
            ->assertJsonPath('related_posts.0.slug', $relatedPost->slug);

        $this->assertDatabaseHas('posts', [
            'user_id' => $this->user->id,
            'category_id' => $this->category1->id,
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
        $this->actingAs($this->user);

        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category1->id,
        ]);

        $post->images()->attach($this->image1->id);

        $response = $this->putJson(route('posts.update', $post->id), [
            'category_id' => $this->category2->id,
            'image_id' => $this->image2->id,
            'title' => 'Updated Title',
            'content' => 'Updated content of the post.',
            'slug' => 'updated-title',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'user_id' => $this->user->id,
                'category_id' => $this->category2->id,
                'image_id' => $this->image2->id,
                'title' => 'Updated Title',
                'content' => 'Updated content of the post.',
                'slug' => 'updated-title',
            ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'category_id' => $this->category2->id,
            'title' => 'Updated Title',
            'content' => 'Updated content of the post.',
            'slug' => 'updated-title',
        ]);

        $this->assertDatabaseHas('imageables', [
           'image_id' => $this->image2->id,
           'imageable_type' => get_class($post),
           'imageable_id' => $post->id,
        ]);
    }

    /**
     * Summary of test_update_with_invalid_data
     * @return void
     */
    public function test_update_with_invalid_data()
    {
        $this->actingAs($this->user);

        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category1->id,
        ]);

        $post->images()->attach($this->image1->id);

        $response = $this->putJson(route('posts.update', $post->id), [
            'category_id' => null,
            'image_id' => null,
            'title' => '',
            'content' => '',
            'slug' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category_id', 'image_id', 'title', 'content', 'slug']);
    }

    public function test_delete_data()
    {
        $this->actingAs($this->user);

        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category1->id,
        ]);

        $post->images()->attach($this->image1->id);

        $response = $this->deleteJson(route('posts.destroy', $post->id));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }
}
