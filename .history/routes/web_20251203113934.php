<?php

use Illuminate\Support\Facades\Route;

// Public Pages
Route::view('/', 'pages.home')->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/privacy-policy', 'pages.privacy')->name('privacy');

// Registration Pages (We will create these views next)
Route::view('/register/selection', 'auth.register-selection')->name('register.selection');
Route::view('/register/donor', 'auth.register-donor')->name('register.donor');
Route::view('/register/organization', 'auth.register-organization')->name('register.organization');

// Default Auth Routes
require __DIR__ . '/auth.php';
