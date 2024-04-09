<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Adjusted dashboard route to use ChatsController
Route::get('/dashboard', [ChatsController::class, 'getUsers'])
     ->middleware(['auth', 'verified'])
     ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Chat Routes
    Route::post('/get-messages', [ChatsController::class, 'index']);
    Route::post('/store-message', [ChatsController::class, 'store']);
    Route::post('/status-messages', [ChatsController::class, 'markAsRead']);
});

require __DIR__.'/auth.php';
