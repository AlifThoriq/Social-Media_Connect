<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Volt::route('/user/{username}', 'user-profile')
->middleware(['auth'])
->name('user.profile');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Volt::route('/explore', 'explore')->middleware(['auth'])->name('explore');


require __DIR__.'/auth.php';
