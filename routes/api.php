<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LevelValidationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });
});

/*
|--------------------------------------------------------------------------
| Level Validation Routes
|--------------------------------------------------------------------------
*/
Route::prefix('levels')->group(function () {
    Route::post('/validate', [LevelValidationController::class, 'validate']);
    Route::post('/reachability', [LevelValidationController::class, 'checkReachability']);
});
