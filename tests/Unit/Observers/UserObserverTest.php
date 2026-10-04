<?php

declare(strict_types=1);

use App\Actions\User\GetUserByUuid;
use App\Actions\User\GetUsers;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

pest()->use(RefreshDatabase::class);

$observer = new UserObserver;

test('creating assigns uuid when empty', function () use ($observer): void {
    $user = new User([
        'name'     => 'Test User',
        'email'    => 'test@example.com',
        'password' => 'password',
    ]);
    $user->uuid = null;

    $observer->creating($user);

    expect($user->uuid)->not->toBeNull()
        ->and(Str::isUuid($user->uuid))->toBeTrue();
});

test('creating does not overwrite uuid when already set', function () use ($observer): void {
    $existingUuid = (string) Str::uuid();
    $user         = new User([
        'name'     => 'Test User',
        'email'    => 'test@example.com',
        'password' => 'password',
    ]);

    $user->uuid = $existingUuid;

    $observer->creating($user);

    expect($user->uuid)->toBe($existingUuid);
});

test('created flushes list cache', function (): void {
    $taggedCache = Mockery::mock();
    $taggedCache->shouldReceive('flush')->once();

    Cache::shouldReceive('tags')
        ->once()
        ->with(User::CACHE_TAG_LIST)
        ->andReturn($taggedCache);

    User::factory()->create();
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

test('updated does nothing when user was not changed', function () use ($observer): void {
    Cache::shouldReceive('tags')->never();

    $user = User::withoutEvents(fn () => User::factory()->create());
    $user->syncOriginal();

    $observer->updated($user);
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

test('creating event assigns uuid when creating user via factory', function (): void {
    $user = User::factory()->create();

    expect($user->uuid)->not->toBeNull()
        ->and(Str::isUuid($user->uuid))->toBeTrue();
});
