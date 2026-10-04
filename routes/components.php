<?php

declare(strict_types=1);

use App\Http\Controllers\ComponentPreviewController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:superadmin'])
    ->prefix('components')
    ->group(function (): void {
        Route::get('/', [ComponentPreviewController::class, 'index'])->name('components.index');
        Route::get('alert', [ComponentPreviewController::class, 'alert'])->name('components.alert');
        Route::get('badges', [ComponentPreviewController::class, 'badges'])->name('components.badges');
        Route::get('buttons', [ComponentPreviewController::class, 'buttons'])->name('components.buttons');
        Route::get('collapsible', [ComponentPreviewController::class, 'collapsible'])->name('components.collapsible');

        Route::prefix('form')->group(function (): void {
            Route::get('basic-input', [ComponentPreviewController::class, 'formBasicInput'])
                ->name('components.form.basic-input');
        });
    });
