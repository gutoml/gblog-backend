<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Summary of test_invalid_data
     * @return void
     */
    public function test_invalid_data(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $this->postJson(route('categories.store'), [
            'name' => '',
            'description' => '',
            'slug' => '',
        ])->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'slug']);
    }

    /**
     * Summary of test_create_category
     * @return void
     */
    public function test_create_category(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('categories.store'), [
            'name' => 'Test Category',
            'description' => 'This is a test category.',
            'slug' => 'test-category',
        ])->assertStatus(201)
        ->assertJsonStructure([
            'id',
            'name',
            'description',
            'slug',
            'created_at',
            'updated_at',
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        $category = \App\Models\Category::find($response->json('id'));
        $this->assertNotNull($category);
        $this->assertEquals('Test Category', $category->name);
        $this->assertEquals('test-category', $category->slug);
        $this->assertEquals('This is a test category.', $category->description);
    }

    /**
     * Summary of test_update_category
     * @return void
     */
    public function test_update_category(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $category = \App\Models\Category::factory()->create([
            'name' => 'Old Category',
            'description' => 'Old description',
            'slug' => 'old-category',
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Old Category',
            'description' => 'Old description',
            'slug' => 'old-category',
        ]);

        $this->putJson(route('categories.update', $category->id), [
            'name' => 'Updated Category',
            'description' => 'This is an updated category.',
            'slug' => 'updated-category',
        ])
        ->assertStatus(200)
        ->assertJson([
            'id' => $category->id,
            'name' => 'Updated Category',
            'description' => 'This is an updated category.',
            'slug' => 'updated-category',
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
            'description' => 'This is an updated category.',
            'slug' => 'updated-category',
        ]);
    }

    public function test_delete_category()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $category = \App\Models\Category::factory()->create([
            'name' => 'Name category to be deleted',
            'description' => 'Description category to be deleted',
            'slug' => 'slug-category-to-be-deleted',
        ]);

        $this->deleteJson(route('categories.destroy', $category->id))
        ->assertStatus(204);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }
}
