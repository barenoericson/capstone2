<?php

use Illuminate\Support\Facades\Route;

// Root route
Route::get('/', fn() => view('app'))->name('home');

// Auth routes
Route::get('/login', fn() => view('app'))->name('login');
Route::get('/register', fn() => view('app'))->name('register');
Route::get('/forgot-password', fn() => view('app'))->name('forgot-password');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', fn() => view('app'))->name('dashboard');
    Route::get('/profile', fn() => view('app'))->name('profile');
    Route::get('/properties', fn() => view('app'))->name('properties');
    Route::get('/saved-properties', fn() => view('app'))->name('saved-properties');
    Route::get('/viewings', fn() => view('app'))->name('viewings');
});

// Catch-all for Vue Router (MUST BE LAST)
Route::get('/{any}', fn() => view('app'))->where('any', '.*')->name('app');