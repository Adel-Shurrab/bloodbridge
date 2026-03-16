<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\TestMatchingController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Http\Controllers\PublicPagesController;

Route::get('/test-matching', [TestMatchingController::class, 'testMatching'])->name('test.matching');
Route::get('/api/test-matching', [TestMatchingController::class, 'testMatchingApi'])->name('api.test.matching');

// تغليف المسارات العامة لدعم اللغات
Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function () {
    // Public Pages
    Route::get('/', [PublicPagesController::class, 'home'])->name('home');
    Route::get('/about', [PublicPagesController::class, 'about'])->name('about');
    Route::get('/contact', [PublicPagesController::class, 'contact'])->name('contact');
    Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'submit'])
        ->name('contact.submit')
        ->middleware('throttle:3,1');
    Route::get('/eligibility', [PublicPagesController::class, 'eligibility'])->name('eligibility');
    Route::get('/terms', [PublicPagesController::class, 'terms'])->name('terms');


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
});
