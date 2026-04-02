<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TmdbController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home.index');
})->name('home');

Route::prefix('tmdb')->name('tmdb.')->group(function () {
    Route::get('/trending', [TmdbController::class, 'trending'])->name('trending');
    Route::get('/search',   [TmdbController::class, 'search'])->name('search');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
