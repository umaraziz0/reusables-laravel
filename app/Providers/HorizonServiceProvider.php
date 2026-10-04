<?php

declare(strict_types=1);

namespace App\Providers;

use Laravel\Horizon\HorizonApplicationServiceProvider;
use Override;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Keep the Horizon gate defined by AdminTool::defineGates() instead of the package default.
     */
    #[Override]
    protected function gate(): void {}

    /**
     * Bootstrap any application services.
     */
    #[Override]
    public function boot(): void
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }
}
