<?php

namespace App\Services\Auth;

use App\Services\Service;

class SignOutCredentialsService implements Service
{
    public function execute(array $data = []): array
    {
        request()->user()->tokens()->delete();

        return [
            'message' => 'Logout realizado com sucesso'
        ];
    }
}
