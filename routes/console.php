<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;
use Laravel\Telescope\Console\PruneCommand as PruneTelescopeEntriesCommand;
use Spatie\Health\Commands\DispatchQueueCheckJobsCommand;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\Commands\ScheduleCheckHeartbeatCommand;

Schedule::command(RunHealthChecksCommand::class)->everyMinute();
Schedule::command(ScheduleCheckHeartbeatCommand::class)->everyMinute();
Schedule::command(DispatchQueueCheckJobsCommand::class)->everyMinute();
Schedule::command('horizon:snapshot')->everyFiveMinutes();
Schedule::command(PruneTelescopeEntriesCommand::class, ['--hours' => 48])->daily();
Schedule::command('activitylog:clean --force')->daily();
