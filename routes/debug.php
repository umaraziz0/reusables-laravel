<?php

declare(strict_types=1);

use App\Http\Controllers\EmailPreviewController;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

Route::middleware(['auth', 'role:superadmin'])
    ->group(function (): void {
        Route::get('health', HealthCheckResultsController::class)->name('health');
        Route::get('emails/failed-job', [EmailPreviewController::class, 'failedJobEmail'])
            ->name('emails.failed-job');
    });
