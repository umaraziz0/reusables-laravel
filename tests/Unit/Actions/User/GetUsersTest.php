<?php

declare(strict_types=1);

use App\Actions\User\GetUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

pest()->use(RefreshDatabase::class);

test('returns all users with default cache attributes', function (): void {
    User::factory()->count(3)->create();

    $action = new GetUsers();
    $users  = $action->handle();

    expect($users)->toHaveCount(3);

    $users->each(function (User $user): void {
        expect($user->getAttributes())->toHaveKeys(User::CACHE_ATTRIBUTES)
            ->and($user->getAttributes())->not->toHaveKey('password');
    });
});

test('caches the result under the users list tag and key', function (): void {
    User::factory()->count(2)->create();

    $action = new GetUsers();
    $action->handle();

    $cached = Cache::tags(User::CACHE_TAG_LIST)->get(User::CACHE_TAG_LIST);

    expect($cached)->not->toBeNull()
        ->and($cached)->toHaveCount(2);
});

test('returns cached result on subsequent calls without querying the database again', function (): void {
    User::factory()->count(2)->create();

    $action = new GetUsers();
    $first  = $action->handle();

    DB::table('users')->insert([
        'uuid'     => (string) Str::uuid(),
        'name'     => 'Bypasses Cache Invalidation',
        'email'    => 'bypass@example.com',
        'password' => 'secret-password',
    ]);

    $second = $action->handle();

    expect($second)->toHaveCount($first->count())
        ->and($second)->toHaveCount(2);
});
