<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;

Route::prefix('v1')->group(function () {
    // Rotas públicas
    Route::name('auth.')->prefix('auth')->group(function () {
        require __DIR__ . '/api/auth.php';

        Route::name('register.')->prefix('register')->group(function () {
            require __DIR__ . '/api/register.php';
        });
    });

    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

    Route::post('/related-post', function(Request $request) {
        dd($request->all());
    });

    // Rotas privadas
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('posts', PostController::class)->except(['show']);
        Route::apiResource('images', ImageController::class)->except(['update']);
    });
});
