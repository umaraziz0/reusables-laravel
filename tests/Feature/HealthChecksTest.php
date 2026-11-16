<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Config;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Facades\Health;

test('the queue check only checks queues that a horizon supervisor works', function (): void {
    $queueCheck = Health::registeredChecks()->first(fn (mixed $check): bool => $check instanceof QueueCheck);
    throw_unless($queueCheck instanceof QueueCheck, LogicException::class, 'No QueueCheck is registered.');

    $workedQueues = collect(Config::array('horizon.defaults'))->pluck('queue')->flatten()->all();

    expect($queueCheck->getQueues())->not->toBeEmpty()
        ->toEqualCanonicalizing($workedQueues)
        ->not->toContain('default');
});
