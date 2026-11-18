<?php

declare(strict_types=1);

use App\Actions\User\GetUserByUuid;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

test('returns the cached user on subsequent calls without querying the database again', function (): void {
    $user = User::factory()->create(['name' => 'Original Name']);

    $action = new GetUserByUuid();
    $action->handle($user->uuid);

    DB::table('users')->where('uuid', $user->uuid)->update(['name' => 'Bypasses Cache Invalidation']);

    expect($action->handle($user->uuid)?->name)->toBe('Original Name');
});
