<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/components.php';
require __DIR__ . '/debug.php';
