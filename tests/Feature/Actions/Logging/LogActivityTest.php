<?php

declare(strict_types=1);

use App\Actions\Logging\LogActivity;
use App\Actions\Logging\RecordActivity;
use App\Jobs\LogActivity as LogActivityJob;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Queue;
use Spatie\Activitylog\Models\Activity;

test('dispatches LogActivity job when not in local environment', function (): void {
    Queue::fake();

    app()->detectEnvironment(fn (): string => 'production');

    $user = User::factory()->create();

    $action = new LogActivity(
        new RecordActivity()
    );
    $attributeChanges = [
        'attributes' => ['status' => 'active'],
        'old'        => ['status' => 'pending'],
    ];
    $action->handle('API', 'Create User', 'user created', $user, $user, ['foo' => 'bar'], $attributeChanges);

    Queue::assertPushed(LogActivityJob::class, fn (LogActivityJob $job): bool => $job->module === 'API'
        && $job->action === 'Create User'
        && $job->message === 'user created'
        && $job->performedOn === $user
        && $job->causedBy === $user
        && $job->withProperties === ['foo' => 'bar']
        && $job->attributeChanges === $attributeChanges
    );
});

test('calls recordActivity directly when in local environment', function (): void {
    app()->detectEnvironment(fn (): string => 'local');

    $user = User::factory()->create();

    $action = new LogActivity(
        new RecordActivity()
    );
    $attributeChanges = [
        'attributes' => ['status' => 'active'],
        'old'        => ['status' => 'pending'],
    ];
    $action->handle('API', 'Create User', 'user created', $user, $user, [], $attributeChanges);

    $activity = Activity::query()->latest()->sole();

    expect($activity->description)->toBe('user created')
        ->and($activity->event)->toBe('API - Create User')
        ->and($activity->attribute_changes?->toArray())->toBe($attributeChanges);
});

test('local recording and dispatched-job handling redact phone values identically', function (): void {
    $user             = User::factory()->create();
    $attributeChanges = ['attributes' => ['phone_number' => '081234567890']];
    $action           = new LogActivity(new RecordActivity());

    app()->detectEnvironment(fn (): string => 'local');
    $action->handle(
        module: 'API',
        action: 'Update User',
        message: 'local phone update',
        performedOn: $user,
        attributeChanges: $attributeChanges,
    );
    $localActivity   = Activity::query()->latest()->sole();
    $localActivityId = $localActivity->getKey();

    Queue::fake();
    app()->detectEnvironment(fn (): string => 'production');
    $action->handle(
        module: 'API',
        action: 'Update User',
        message: 'queued phone update',
        performedOn: $user,
        attributeChanges: $attributeChanges,
    );

    $queuedJob = null;
    Queue::assertPushed(LogActivityJob::class, function (LogActivityJob $job) use (&$queuedJob): bool {
        $queuedJob = $job;

        return true;
    });

    $queuedJob?->handle(resolve(RecordActivity::class));
    $queuedActivity = Activity::query()
        ->whereKeyNot($localActivityId)
        ->sole();

    expect($localActivity->attribute_changes?->toArray())->toBe([
        'attributes' => ['phone_number' => '********7890'],
    ])
        ->and($queuedActivity->attribute_changes?->toArray())->toBe(
            $localActivity->attribute_changes?->toArray(),
        );
});

test('uses authenticated user as default causedBy', function (): void {
    app()->detectEnvironment(fn (): string => 'local');

    $user = User::factory()->create();
    Auth::login($user);

    $action = new LogActivity(
        new RecordActivity()
    );
    $action->handle('API', 'Update Profile', 'profile updated', $user, null, []);

    $activity = Activity::query()->latest()->sole();

    expect($activity->causer_id)->toBe((int) $user->id);
});
