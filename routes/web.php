<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Http\Controllers\PublicPagesController;
use App\Http\Controllers\Auth\VerifyEmailController;

// Verification route must be outside the localization group so the signed URL
// is not broken by the localizationRedirect middleware switching locale prefixes.
Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

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

    // Default Auth Routes
    require __DIR__ . '/auth.php';
});
