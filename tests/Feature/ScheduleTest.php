<?php

declare(strict_types=1);

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;

/**
 * @return array<string, string>
 */
function scheduledCommandExpressions(): array
{
    return collect(resolve(Schedule::class)->events())
        ->mapWithKeys(fn (Event $event): array => [
            (string) str($event->command)->after("'artisan' ") => $event->expression,
        ])
        ->all();
}

test('health check heartbeats run every minute so the schedule and queue checks can pass', function (string $command): void {
    expect(scheduledCommandExpressions())->toHaveKey($command, '* * * * *');
})->with([
    'health:check',
    'health:schedule-check-heartbeat',
    'health:queue-check-heartbeat',
]);
