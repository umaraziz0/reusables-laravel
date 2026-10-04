<?php

declare(strict_types=1);

use App\Actions\Logging\RecordActivity;
use App\Jobs\LogActivity;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Spatie\Activitylog\Models\Activity;

test('job is queued on logs_activity queue when dispatched', function (): void {
    Queue::fake();

    $user = User::factory()->create();

    dispatch(new LogActivity(
        module: 'API',
        action: 'Create User',
        message: 'user was created',
        performedOn: $user,
        causedBy: $user,
        withProperties: ['foo' => 'bar'],
    ));

    $queueName = Config::string('queue.queues.logs_activity');

    Queue::assertPushedOn($queueName, LogActivity::class);
});

test('job encrypts and redacts payload before serialization', function (): void {
    $sentinel = 'raw-secret-sentinel';
    $user     = User::factory()->make();
    $user->makeHidden('dynamicSecret');

    $job = new LogActivity(
        module: 'API',
        action: 'Update User',
        message: 'user was updated',
        performedOn: $user,
        causedBy: null,
        withProperties: [
            'dynamicSecret' => $sentinel,
            'passwordHash'  => $sentinel,
            'nested'        => ['credentials' => $sentinel],
        ],
        attributeChanges: [
            'attributes' => ['dynamicSecret' => $sentinel],
            'old'        => ['userPassword' => $sentinel],
        ],
    );

    expect($job->withProperties)->toBe([
        'dynamicSecret' => '[REDACTED]',
        'passwordHash'  => '[REDACTED]',
        'nested'        => ['credentials' => '[REDACTED]'],
    ])
        ->and($job->attributeChanges)->toBe([
            'attributes' => ['dynamicSecret' => '[REDACTED]'],
            'old'        => ['userPassword' => '[REDACTED]'],
        ])
        ->and(serialize($job))->not->toContain($sentinel);
});

test('job handle persists an activity with the expected details', function (): void {
    $user = User::factory()->create();

    $job = new LogActivity(
        module: 'API',
        action: 'Create User',
        message: 'user was created',
        performedOn: $user,
        causedBy: $user,
        withProperties: ['foo' => 'bar'],
        attributeChanges: [
            'attributes' => ['status' => 'active'],
            'old'        => ['status' => 'pending'],
        ],
    );

    $job->handle(resolve(RecordActivity::class));

    $activity = Activity::query()->sole();

    expect($activity->event)->toBe('API - Create User')
        ->and($activity->description)->toBe('user was created')
        ->and($activity->subject_id)->toBe((int) $user->id)
        ->and($activity->causer_id)->toBe((int) $user->id)
        ->and($activity->properties?->toArray())->toBe(['foo' => 'bar'])
        ->and($activity->attribute_changes?->toArray())->toBe([
            'attributes' => ['status' => 'active'],
            'old'        => ['status' => 'pending'],
        ]);
});

test('job handles null performedOn and causedBy', function (): void {
    $job = new LogActivity(
        module: 'Jobs',
        action: 'Background Task',
        message: 'background task completed',
        performedOn: null,
        causedBy: null,
        withProperties: [],
    );

    $job->handle(resolve(RecordActivity::class));

    $activity = Activity::query()->latest()->first();

    expect($activity)->not->toBeNull()
        ->and($activity->description)->toBe('background task completed')
        ->and($activity->event)->toBe('Jobs - Background Task')
        ->and($activity->subject_id)->toBeNull()
        ->and($activity->causer_id)->toBeNull();
});

test('job uses default queue when logs_activity config is null', function (): void {
    $originalConfig = Config::string('queue.queues.logs_activity');

    config(['queue.queues.logs_activity' => null]);

    Queue::fake();

    dispatch(new LogActivity(
        module: 'Jobs',
        action: 'Test Action',
        message: 'test action ran',
        performedOn: null,
        causedBy: null,
    ));

    Queue::assertPushedOn('default', LogActivity::class);

    config(['queue.queues.logs_activity' => $originalConfig]);
});
