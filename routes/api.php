<?php

use App\Http\Controllers\Api\LevelValidationController;
use Illuminate\Support\Facades\Route;

Route::prefix('levels')->group(function () {
    Route::post('/validate', [LevelValidationController::class, 'validate']);
    Route::post('/reachability', [LevelValidationController::class, 'checkReachability']);
});
