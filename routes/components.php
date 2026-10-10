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
            Route::get('input-field', [ComponentPreviewController::class, 'formInputField'])
                ->name('components.form.input-field');
            Route::get('select-field', [ComponentPreviewController::class, 'formSelectField'])
                ->name('components.form.select-field');
            Route::get('search-select-field', [ComponentPreviewController::class, 'formSearchSelectField'])
                ->name('components.form.search-select-field');
        });
    });
