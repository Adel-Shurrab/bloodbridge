<?php

use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Public Pages
Route::view('/', 'pages.home')->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/privacy-policy', 'pages.privacy')->name('privacy');

// Registration Pages
Route::get('/register-selection', [AuthenticatedSessionController::class, 'create'])->name('register.selection');

// Default Auth Routes
require __DIR__ . '/auth.php';
