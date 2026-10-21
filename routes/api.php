<?php

use App\Infrastructure\Http\Controllers\AuthController;
use App\Infrastructure\Http\Controllers\FavoriteGifController;
use App\Infrastructure\Http\Controllers\GifController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Authentication Routes
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/auth/login', [AuthController::class, 'login'])->name('api.auth.login');

// Authenticated Routes (OAuth2 / Passport)
Route::middleware('auth:api')->group(function () {
    // Current User Profile
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    })->name('api.user');

    // GIF Services
    Route::get('/gifs/search', [GifController::class, 'search'])->name('api.gifs.search');
    Route::get('/gifs/{id}', [GifController::class, 'show'])->name('api.gifs.show');

    // Favorite GIF Services
    Route::post('/gifs/favorites', [FavoriteGifController::class, 'store'])->name('api.gifs.favorites.store');
    Route::post('/favorites', [FavoriteGifController::class, 'store'])->name('api.favorites.store');
});
