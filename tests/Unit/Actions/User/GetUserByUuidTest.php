<?php

declare(strict_types=1);

use App\Actions\User\GetUserByUuid;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

pest()->use(RefreshDatabase::class);

test('returns the user matching the given uuid', function (): void {
    $user = User::factory()->create();

    $action = new GetUserByUuid();
    $result = $action->handle($user->uuid);

    expect($result)->not->toBeNull()
        ->and($result->uuid)->toBe($user->uuid)
        ->and($result->getAttributes())->toHaveKeys(User::CACHE_ATTRIBUTES);
});

test('returns null when no user matches the given uuid', function (): void {
    $action = new GetUserByUuid();
    $result = $action->handle((string) Str::uuid());

    expect($result)->toBeNull();
});

test('caches the result under the per-uuid cache key', function (): void {
    $user = User::factory()->create();

    $action = new GetUserByUuid();
    $action->handle($user->uuid);

    $cacheKey = User::getCacheKeyForUuid($user->uuid);

    $cached = Cache::tags(User::CACHE_TAG_ITEM)->get($cacheKey);

    expect($cached)->not->toBeNull()
        ->and($cached->uuid)->toBe($user->uuid);
});

test('returns fresh result on subsequent calls after the record changes', function (): void {
    $user = User::factory()->create(['name' => 'Original Name']);

    $action = new GetUserByUuid();
    $first  = $action->handle($user->uuid);

    $user->update(['name' => 'Updated Name']);

    $second = $action->handle($user->uuid);

    expect($first->name)->toBe('Original Name')
        ->and($second->name)->toBe('Updated Name');
});

test('returns null after a cached user is deleted', function (): void {
    $user = User::factory()->create();

    $action = new GetUserByUuid();
    $action->handle($user->uuid);

    $user->delete();

    expect($action->handle($user->uuid))->toBeNull();
});
