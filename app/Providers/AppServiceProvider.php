<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\AdminTool;
use App\Enums\Role as RoleEnum;
use App\Jobs\NotifyFailedJob;
use App\Models\User;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Nightwatch\Facades\Nightwatch;
use Laravel\Nightwatch\Records\Query;
use Laravel\Nightwatch\Records\QueuedJob;
use Laravel\Telescope\Jobs\ProcessPendingUpdates;
use Override;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $isLocal   = app()->isLocal();
        $isTesting = app()->runningUnitTests();

        $this->configureDefaults($isLocal, $isTesting);
        $this->configureScramble();
        $this->configureApiRateLimiting();
        $this->configureAuthorization();
        $this->configureNightwatchFilters();
        $this->registerFailedJobNotification();
    }

    private function configureDefaults(bool $isLocal, bool $isTesting): void
    {
        DB::prohibitDestructiveCommands(
            ! $isLocal && ! $isTesting,
        );

        Password::defaults(
            fn (): Password => Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
        );
    }

    private function configureScramble(): void
    {
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi): void {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });
    }

    private function configureApiRateLimiting(): void
    {
        RateLimiter::for('api', fn (Request $request): Limit => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));
    }

    private function configureAuthorization(): void
    {
        Gate::before(function (?User $user, string $ability): ?bool {
            if ($user?->hasRole(RoleEnum::SUPERADMIN->value)) {
                return true;
            }

            return null;
        });

        AdminTool::defineGates();
    }

    private function configureNightwatchFilters(): void
    {
        Nightwatch::rejectQueuedJobs(fn (QueuedJob $job): bool => $job->name === ProcessPendingUpdates::class);

        Nightwatch::rejectQueries(fn (Query $query): bool => str_contains($query->sql, 'telescope_')
            || str_contains($query->sql, 'pulse_'));
    }

    private function registerFailedJobNotification(): void
    {
        Queue::failing(function (JobFailed $event): void {
            $payload  = $event->job->payload();
            $jobClass = $payload['data']['commandName'] ?? null;

            if ($jobClass === NotifyFailedJob::class) {
                return;
            }

            $notificationEmails = Config::array('queue.failed.notification_emails', []);

            if (empty($notificationEmails)) {
                return;
            }

            dispatch(new NotifyFailedJob(
                connectionName: $event->connectionName,
                failedQueue: $event->job->getQueue(),
                uuid: $payload['uuid'] ?? null,
                jobName: $payload['displayName'] ?? $jobClass ?? 'Unknown',
                exceptionClass: $event->exception::class,
                exceptionMessage: $event->exception->getMessage(),
                stackTrace: $event->exception->getTraceAsString(),
                failedAt: now()->toIso8601String(),
                attempts: $event->job->attempts()
            ));
        });
    }
}
