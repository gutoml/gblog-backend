<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostHighlineController;
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

    Route::get('/home', HomeController::class)->name('home');

    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show')->middleware('track.view');

    Route::get('/post-highline', [PostHighlineController::class, 'index'])->name('post-highline.index');

    // Rotas privadas
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('posts', PostController::class)->except(['index', 'show']);
        Route::apiResource('images', ImageController::class)->except(['update']);
        Route::apiResource('post-highline', PostHighlineController::class)->except(['index', 'update']);
        Route::put('/post-highline', [PostHighlineController::class, 'update'])->name('post-highline.update');
    });
});
