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

// Route Rahasia untuk Eksekusi Perintah Terminal dari Browser
Route::get('/hajar-db', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
            '--seed' => true,
            '--force' => true
        ]);
        return 'Database Neon.tech Sukses Terhajar dan Terisi Data!';
    } catch (\Exception $e) {
        return 'Waduh error bro: ' . $e->getMessage();
    }
});

require __DIR__.'/auth.php';
