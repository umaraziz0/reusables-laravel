<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;
use Spatie\Health\Commands\RunHealthChecksCommand;

Schedule::command(RunHealthChecksCommand::class)->everyMinute();
Schedule::command('horizon:snapshot')->everyFiveMinutes();
Schedule::command('activitylog:clean --force')->daily();
