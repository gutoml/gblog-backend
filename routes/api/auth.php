<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::prefix('signin')->name('singin.')->group(function () {
    Route::post('/', [AuthController::class, 'signinCredential'])->name('credentials');
});

Route::middleware('auth:sanctum')->name('signout.')->prefix('signout')->group(function () {
    Route::post('/', [AuthController::class, 'signoutCredential'])->name('credentials');
});
