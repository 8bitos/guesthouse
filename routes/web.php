<?php

use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/rooms', function () {
    return view('pages.rooms');
})->name('rooms');

Route::get('/gallery', function () {
    return view('pages.gallery');
})->name('gallery');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

Route::get('/booking', function () {
    return view('pages.booking');
})->name('booking');
