<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Override;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DatabaseConnectionCountCheck;
use Spatie\Health\Checks\Checks\DatabaseSizeCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\HorizonCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\RedisCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Facades\Health;

class HealthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    #[Override]
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $connection = Config::string('database.default');

        Health::checks([
            // --- Config Checks --- //

            DebugModeCheck::new(),

            EnvironmentCheck::new(),

            ScheduleCheck::new(),

            OptimizedAppCheck::new(),

            // --- DB Checks --- //

            DatabaseCheck::new()
                ->name('Database')
                ->connectionName($connection),

            DatabaseCheck::new()
                ->name('Telescope Database')
                ->connectionName('telescope_' . $connection),

            DatabaseCheck::new()
                ->name('Pulse Database')
                ->connectionName('pulse_' . $connection),

            DatabaseCheck::new()
                ->name('Health Database')
                ->connectionName('health_' . $connection),

            DatabaseConnectionCountCheck::new()
                ->connectionName($connection)
                ->warnWhenMoreConnectionsThan(50)
                ->failWhenMoreConnectionsThan(100),

            DatabaseSizeCheck::new()
                ->connectionName($connection)
                ->failWhenSizeAboveGb(errorThresholdGb: 5.0),

            // --- Redis Checks --- //

            RedisCheck::new(),

            CacheCheck::new(),

            HorizonCheck::new(),

            QueueCheck::new()
                ->onQueue([
                    'default',
                    Config::string('queue.queues.emails'),
                    Config::string('queue.queues.storage'),
                    Config::string('queue.queues.logs_message'),
                    Config::string('queue.queues.logs_activity'),
                    Config::string('queue.queues.telescope'),
                    Config::string('queue.queues.notifications'),
                ])
                ->failWhenHealthJobTakesLongerThanMinutes(10),
        ]);
    }
}
