<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\TelescopeServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider as TelescopePackageServiceProvider;

test('telescope is not registered outside local', function (): void {
    expect(app()->providerIsLoaded(TelescopePackageServiceProvider::class))->toBeFalse()
        ->and(app()->providerIsLoaded(TelescopeServiceProvider::class))->toBeFalse();
});

test('telescope is registered locally', function (): void {
    app()->instance('env', 'local');

    new AppServiceProvider(app())->register();

    expect(app()->providerIsLoaded(TelescopePackageServiceProvider::class))->toBeTrue()
        ->and(app()->providerIsLoaded(TelescopeServiceProvider::class))->toBeTrue();
});
