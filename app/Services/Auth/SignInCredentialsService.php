<?php

namespace App\Services\Auth;

use Exception;
use App\Services\Service;
use Illuminate\Support\Facades\Auth;

class SignInCredentialsService implements Service
{
    public function execute(array $data): array
    {
        if (!Auth::attempt($data)) {
            throw new Exception('Invalid credentials', 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ];
    }
}
