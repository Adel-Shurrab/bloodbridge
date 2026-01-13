<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

use App\Http\Controllers\PublicPagesController;

// Public Pages
Route::get('/', [PublicPagesController::class, 'home'])->name('home');
Route::get('/about', [PublicPagesController::class, 'about'])->name('about');
Route::get('/contact', [PublicPagesController::class, 'contact'])->name('contact');

// Registration Pages
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('register_selection', [RegisteredUserController::class, 'showRegisterSelection'])
        ->name('register.selection');

    // Donor Registration
    Route::get('register/donor', [RegisteredUserController::class, 'showDonorRegistrationForm'])
        ->name('register.donor');
    Route::post('register/donor', [RegisteredUserController::class, 'storeDonor'])
        ->name('register.donor.store');

    // Organization Registration
    Route::get('register/organization', [RegisteredUserController::class, 'showOrganizationRegistrationForm'])
        ->name('register.organization');
    Route::post('register/organization', [RegisteredUserController::class, 'storeOrganization'])
        ->name('register.organization.store');
});
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
// Default Auth Routes
require __DIR__ . '/auth.php';
