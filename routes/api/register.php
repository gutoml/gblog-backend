<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;

Route::post('/', [RegisterController::class, 'credentials'])->name('credentials');
