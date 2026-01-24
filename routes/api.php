<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LevelController;
use App\Http\Controllers\Api\LevelValidationController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\TileController;
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
| Player Routes (Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/player', [PlayerController::class, 'show']);
    Route::post('/player', [PlayerController::class, 'store']);
    Route::put('/player', [PlayerController::class, 'update']);
    Route::delete('/player', [PlayerController::class, 'destroy']);
    Route::get('/player/progress', [PlayerController::class, 'progress']);
});

/*
|--------------------------------------------------------------------------
| Level Routes
|--------------------------------------------------------------------------
*/
Route::prefix('levels')->group(function () {
    Route::get('/', [LevelController::class, 'index']);
    Route::get('/{level}', [LevelController::class, 'show']);
    Route::post('/validate', [LevelValidationController::class, 'validate']);
    Route::post('/reachability', [LevelValidationController::class, 'checkReachability']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/{level}/complete', [LevelController::class, 'complete']);
    });
});

/*
|--------------------------------------------------------------------------
| Tile Routes
|--------------------------------------------------------------------------
*/
Route::get('/tiles', [TileController::class, 'index']);
