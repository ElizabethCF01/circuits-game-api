<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('userzone.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/player', [App\Http\Controllers\Userzone\PlayerController::class, 'show'])->name('userzone.player.show');
    Route::get('/player/create', [App\Http\Controllers\Userzone\PlayerController::class, 'create'])->name('userzone.player.create');
    Route::post('/player', [App\Http\Controllers\Userzone\PlayerController::class, 'store'])->name('userzone.player.store');
    Route::get('/player/edit', [App\Http\Controllers\Userzone\PlayerController::class, 'edit'])->name('userzone.player.edit');
    Route::patch('/player', [App\Http\Controllers\Userzone\PlayerController::class, 'update'])->name('userzone.player.update');
});

Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('tiles', App\Http\Controllers\TileController::class);
    Route::resource('players', App\Http\Controllers\PlayerController::class);
    Route::resource('levels', App\Http\Controllers\LevelController::class);
});

require __DIR__.'/auth.php';
