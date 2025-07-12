<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// IMPORTANT: Place your custom auth routes BEFORE requiring auth.php
Route::middleware('guest')->group(function () {
    // Override the default register routes
    Route::get('/register', function () {
        return Inertia::render('Auth/Register');
    })->name('register');
    
    Route::post('/register', [AuthController::class, 'register']);
    
    // Verification routes
    Route::get('/verify-registration', [AuthController::class, 'showVerificationForm'])
        ->name('verify-registration');
    
    Route::post('/verify-registration', [AuthController::class, 'verifyAndCompleteRegistration'])
        ->name('verify-registration.submit');
    
    Route::post('/verification/resend', [AuthController::class, 'resendVerificationCode'])
        ->name('verification.resend');
});

// Include Laravel's default auth routes (login, logout, etc.)
// But your custom register routes above will take precedence
require __DIR__.'/auth.php';