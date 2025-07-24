<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\AuthRequest;
use App\Services\Auth\SignInCredentialsService;
use App\Services\Auth\SignOutCredentialsService;

class AuthController extends Controller
{
    public function signinCredential(AuthRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $signInCredentialsService = new SignInCredentialsService();
            $response = $signInCredentialsService->execute($request->validated());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }

        return response()->json($response);
    }

    public function signoutCredentials(Request $request): \Illuminate\Http\JsonResponse
    {
        $signOutCredentialsService = new SignOutCredentialsService();
        $message = $signOutCredentialsService->execute();

        return response()->json($message);
    }
}
