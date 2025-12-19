<?php

use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

// Public Pages
Route::view('/', 'pages.home')->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/privacy-policy', 'pages.privacy')->name('privacy');

// Registration Pages
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('register_selection', [RegisteredUserController::class, 'showRegisterSelection'])
        ->name('register');

    // Donor Registration
    Route::get('register/donor', [RegisteredUserController::class, 'showDonorRegistrationForm'])
        ->name('register.donor');
    Route::post('register/donor', [RegisteredUserController::class, 'storeDonor'])
        ->name('register.donor.store');
});
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
    Route::get('admin')
});
// Default Auth Routes
require __DIR__ . '/auth.php';
