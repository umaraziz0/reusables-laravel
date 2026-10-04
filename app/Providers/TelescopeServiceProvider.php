<?php

declare(strict_types=1);

namespace App\Providers;

use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;
use Override;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters([
            '_token',
            'password',
            'password_confirmation',
            'name',
            'email',
            'phone_number',
            'captcha',
        ]);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Keep the Telescope gate defined by AdminTool::defineGates() instead of the package default.
     */
    #[Override]
    protected function gate(): void {}

    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        $isLocal = $this->app->environment('local');

        Telescope::filter(function (IncomingEntry $entry) use ($isLocal): bool {
            if ($isLocal) {
                return true;
            }

            if ($entry->isReportableException()) {
                return true;
            }

            if ($entry->isFailedRequest()) {
                return true;
            }

            if ($entry->isFailedJob()) {
                return true;
            }

            if ($entry->isScheduledTask()) {
                return true;
            }

            return $entry->hasMonitoredTag();
        });
    }
}
