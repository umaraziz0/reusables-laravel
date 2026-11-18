<?php

declare(strict_types=1);

use App\Actions\User\GetUserByUuid;
use App\Actions\User\GetUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

pest()->use(RefreshDatabase::class);

test('creating keeps a uuid that is already set', function (): void {
    $existingUuid = (string) Str::uuid();

    $user = User::factory()->create(['uuid' => $existingUuid]);

    expect($user->uuid)->toBe($existingUuid);
});

test('creating a user flushes the list cache', function (): void {
    $listAction = new GetUsers();
    $listAction->handle();

    $user = User::factory()->create();

    expect($listAction->handle()->pluck('uuid')->all())->toBe([$user->uuid]);
});

test('updated invalidates uuid cache and flushes list when user was changed', function (): void {
    $user       = User::factory()->create(['name' => 'Original Name']);
    $itemAction = new GetUserByUuid();
    $listAction = new GetUsers();
    $itemAction->handle($user->uuid);
    $listAction->handle();

    $user->update(['name' => 'Updated Name']);

    expect($itemAction->handle($user->uuid)?->name)->toBe('Updated Name')
        ->and($listAction->handle()->first()?->name)->toBe('Updated Name');
});

test('deleted invalidates uuid cache and flushes list when user has uuid', function (): void {
    $user       = User::factory()->create();
    $itemAction = new GetUserByUuid();
    $listAction = new GetUsers();
    $itemAction->handle($user->uuid);
    $listAction->handle();

    $user->delete();

    expect($itemAction->handle($user->uuid))->toBeNull()
        ->and($listAction->handle())->toBeEmpty();
});

test('deleted only flushes list when user has null uuid', function (): void {
    $user       = User::factory()->create();
    $listAction = new GetUsers();
    $listAction->handle();

    $user->uuid = null;
    $user->delete();

    expect($listAction->handle())->toBeEmpty();
});

test('restoring a user flushes list cache', function (): void {
    $user       = User::factory()->trashed()->create();
    $listAction = new GetUsers();
    $listAction->handle();

    $user->restore();

    expect($listAction->handle()->pluck('uuid')->all())->toBe([$user->uuid]);
});

test('creating event assigns uuid when creating user via factory', function (): void {
    $user = User::factory()->create();

    expect($user->uuid)->not->toBeNull()
        ->and(Str::isUuid($user->uuid))->toBeTrue();
});
