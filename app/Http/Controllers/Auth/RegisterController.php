<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\RegisterNewUserService;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function credentials(RegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $registerNewUserService = new RegisterNewUserService();
            $response = $registerNewUserService->execute($request->validated());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }

        return response()->json($response, 201);
    }
}
