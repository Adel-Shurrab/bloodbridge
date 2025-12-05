<?php

use Illuminate\Support\Facades\Route;

// Public Pages
Route::view('/', 'pages.home')->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/privacy-policy', 'pages.privacy')->name('privacy');

// Registration Pages
Route::view('/register-selection', 'auth.register-selection')->name('register.selection');

// Default Auth Routes
require __DIR__ . '/auth.php';
