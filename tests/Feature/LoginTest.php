<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test successful login with valid credentials.
     */
    public function test_successful_login(): void
    {
        $password = 'Password#123';

        $user = \App\Models\User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $this->postJson(route('auth.singin.credentials'), [
            'email' => $user->email,
            'password' => $password,
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->assertJsonStructure([
            'user' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
            ],
            'access_token',
            'token_type',
        ])->assertStatus(200);
    }
    /**
     * Test login failure with invalid credentials.
     */
    public function test_error_on_invalid_data(): void
    {
        $this->postJson(route('auth.singin.credentials'), [
            'email' => 'email@email.com',
            'password' => 'AlgumaSenhaAleatoria#389127',
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->assertJson([
            'error' => 'Invalid credentials.',
        ])->assertStatus(401);
    }

    public function test_with_empty_fields()
    {
        $this->postJson(route('auth.singin.credentials'), [
            'email' => '',
            'password' => '',
        ], [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->assertJson(function(AssertableJson $json) {
            $json->has('errors')
                ->where('errors.email.0', 'O e-mail é obrigatório.')
                ->where('errors.password.0', 'A senha é obrigatória.')
                ->etc();
        })->assertStatus(422);
    }
}
