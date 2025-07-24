<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Summary of test_with_valid_data
     * @return void
     */
    public function test_with_valid_data(): void
    {
        $password = 'Abofete#$38791239';

        $this->postJson(route('auth.register.credentials'), [
            'name' => 'Test User',
            'email' => 'email@email.com',
            'password' => $password,
            'password_confirmation' => $password,
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->assertJsonPath('message', 'User registered successfully')
        ->assertJsonStructure([
            'message',
            'user' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
        ])->assertStatus(201);
    }

    public function test_error_on_invalid_data(): void
    {
        $this->postJson(route('auth.register.credentials'), [
            'name' => 'Test',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'short',
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->assertJsonValidationErrors(['name', 'email', 'password'])
          ->assertStatus(422);
    }

    public function test_error_on_empty_data(): void
    {
        $this->postJson(route('auth.register.credentials'), [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->assertJsonValidationErrors(['name', 'email', 'password'])
          ->assertStatus(422);
    }
}
