<?php

declare(strict_types=1);

use App\Actions\Logging\RecordActivity;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

dataset('one-sided attribute changes', [
    'attributes only' => [
        ['attributes' => ['status' => 'active']],
        ['attributes' => ['status' => 'active']],
    ],
    'old only' => [
        ['old' => ['status' => 'pending']],
        ['old' => ['status' => 'pending']],
    ],
]);

test('creates activity log entry with performedOn and causedBy', function (): void {
    $user = User::factory()->create();

    $action = new RecordActivity();
    $action->handle(
        module: 'API',
        action: 'Create User',
        message: 'user was created',
        performedOn: $user,
        causedBy: $user,
        withProperties: ['key' => 'value']
    );

    $activity = Activity::query()->sole();

    expect($activity->description)->toBe('user was created')
        ->and($activity->event)->toBe('API - Create User')
        ->and($activity->properties->toArray())->toBe(['key' => 'value'])
        ->and($activity->subject_id)->toBe((int) $user->id)
        ->and($activity->causer_id)->toBe((int) $user->id);
});

test('persists sanitized new and old attribute changes in standard shape', function (): void {
    $user = User::factory()->create();

    $action = new RecordActivity();
    $action->handle(
        module: 'API',
        action: 'Update User',
        message: 'user was updated',
        performedOn: $user,
        causedBy: $user,
        withProperties: [
            'email'    => 'johndoe@mail.com',
            'metadata' => ['password' => 'secret'],
        ],
        attributeChanges: [
            'attributes' => [
                'email'       => 'new@example.com',
                'password'    => 'new-password',
                'secretary'   => 'safe',
                'phoneNumber' => '081234567890',
            ],
            'old' => [
                'email'    => 'old@example.com',
                'password' => 'old-password',
            ],
        ],
    );

    $activity = Activity::query()->sole();

    expect($activity->attribute_changes?->toArray())->toBe([
        'attributes' => [
            'email'       => 'n*w@*******.com',
            'password'    => '[REDACTED]',
            'secretary'   => 'safe',
            'phoneNumber' => '********7890',
        ],
        'old' => [
            'email'    => 'o*d@*******.com',
            'password' => '[REDACTED]',
        ],
    ])
        ->and($activity->properties?->toArray())->toBe([
            'email'    => 'j*****e@****.com',
            'metadata' => ['password' => '[REDACTED]'],
        ]);
});

test('does not persist empty attribute changes', function (): void {
    $action = new RecordActivity();
    $action->handle(
        module: 'API',
        action: 'Update User',
        message: 'nothing changed',
        attributeChanges: ['attributes' => [], 'old' => []],
    );

    $activity = Activity::query()->sole();

    expect($activity->attribute_changes?->toArray())->toBe([]);
});

test('persists one-sided attribute changes without adding missing branches', function (array $attributeChanges, array $expected): void {
    $action = new RecordActivity();
    $action->handle(
        module: 'API',
        action: 'Update User',
        message: 'user changed',
        attributeChanges: $attributeChanges,
    );

    $activity = Activity::query()->sole();

    expect($activity->attribute_changes?->toArray())->toBe($expected);
})->with('one-sided attribute changes');

test('does not calculate changes from a dirty performedOn model', function (): void {
    $user       = User::factory()->create();
    $user->name = 'Changed Name';

    $action = new RecordActivity();
    $action->handle(
        module: 'API',
        action: 'Update User',
        message: 'user changed',
        performedOn: $user,
    );

    $activity = Activity::query()->sole();

    expect($activity->attribute_changes?->toArray())->toBe([]);
});

test('stores module and action as the activity event', function (): void {
    $action = new RecordActivity();
    $action->handle(
        module: 'Jobs',
        action: 'Process Queue',
        message: 'background task ran',
    );

    $activity = Activity::query()->sole();

    expect($activity->event)->toBe('Jobs - Process Queue')
        ->and($activity->description)->toBe('background task ran');
});

test('handles null performedOn and causedBy', function (): void {
    $action = new RecordActivity();
    $action->handle(
        module: 'Jobs',
        action: 'Background Task',
        message: 'background task ran',
        withProperties: [],
    );

    $activity = Activity::query()->sole();

    expect($activity->subject_id)->toBeNull()
        ->and($activity->causer_id)->toBeNull();
});
