<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Arr;
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
                ->onQueue($this->horizonQueues())
                ->failWhenHealthJobTakesLongerThanMinutes(10),
        ]);
    }

    /**
     * The queues Horizon's supervisors work. Checking any other queue would pile
     * heartbeat jobs onto a queue no worker reads.
     *
     * @return list<string>
     */
    private function horizonQueues(): array
    {
        $queues = collect(Config::array('horizon.defaults'))
            ->flatMap(fn (mixed $supervisor): array => is_array($supervisor) ? Arr::wrap($supervisor['queue'] ?? []) : [])
            ->filter(fn (mixed $queue): bool => is_string($queue))
            ->unique()
            ->all();

        return array_values($queues);
    }
}
