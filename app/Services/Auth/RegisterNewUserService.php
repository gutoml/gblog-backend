<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\Service;

class RegisterNewUserService implements Service
{
    public function execute(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        if (!$user) {
            throw new \Exception('User registration failed', 500);
        }

        return [
            'message' => 'User registered successfully',
            'user' => $user
        ];
    }
}
