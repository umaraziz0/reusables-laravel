<?php

declare(strict_types=1);

use App\Enums\Ability\UserAbility;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

// --- Public API --- //

// --- Private API --- //

Route::middleware(['ip_whitelist', 'auth:sanctum'])
    ->group(function (): void {
        // --- Users --- //

        Route::prefix('users')
            ->name('users.')
            ->group(function (): void {
                Route::get('/', [UserController::class, 'index'])
                    ->middleware('abilities:' . UserAbility::View->value)
                    ->name('index');

                Route::get('/paginated', [UserController::class, 'indexPaginated'])
                    ->middleware('abilities:' . UserAbility::View->value)
                    ->name('index.paginated');

                Route::post('/', [UserController::class, 'store'])
                    ->middleware('abilities:' . UserAbility::Create->value)
                    ->name('store');

                Route::get('/{uuid}', [UserController::class, 'show'])
                    ->middleware('abilities:' . UserAbility::View->value)
                    ->whereUuid('uuid')
                    ->name('show');
            });
    });
