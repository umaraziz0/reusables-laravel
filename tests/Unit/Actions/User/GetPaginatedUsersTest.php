<?php

declare(strict_types=1);

use App\Actions\User\GetPaginatedUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

pest()->use(RefreshDatabase::class);

test('returns paginated users with default page and per page', function (): void {
    User::factory()->count(15)->create();

    $action    = new GetPaginatedUsers();
    $paginator = $action->handle();

    expect($paginator->currentPage())->toBe(1)
        ->and($paginator->perPage())->toBe(10)
        ->and($paginator->items())->toHaveCount(10)
        ->and($paginator->total())->toBe(15);
});

test('respects given page and per page parameters', function (): void {
    User::factory()->count(15)->create();

    $action    = new GetPaginatedUsers();
    $paginator = $action->handle(page: 2, perPage: 5);

    expect($paginator->currentPage())->toBe(2)
        ->and($paginator->perPage())->toBe(5)
        ->and($paginator->items())->toHaveCount(5);
});

test('orders users by id ascending', function (): void {
    $userWithId30     = User::factory()->make();
    $userWithId30->id = 30;
    $userWithId30->save();

    $userWithId10     = User::factory()->make();
    $userWithId10->id = 10;
    $userWithId10->save();

    $userWithId20     = User::factory()->make();
    $userWithId20->id = 20;
    $userWithId20->save();

    $paginator = new GetPaginatedUsers()->handle(perPage: 3);

    expect(collect($paginator->items())->pluck('uuid')->all())->toBe([
        $userWithId10->uuid,
        $userWithId20->uuid,
        $userWithId30->uuid,
    ]);
});

test('returns a cached page on subsequent calls without querying the database again', function (): void {
    User::factory()->count(3)->create();

    $action = new GetPaginatedUsers();
    $action->handle(page: 1, perPage: 10);

    DB::table('users')->insert([
        'uuid'     => (string) Str::uuid(),
        'name'     => 'Bypasses Cache Invalidation',
        'email'    => 'bypass@example.com',
        'password' => 'secret-password',
    ]);

    expect($action->handle(page: 1, perPage: 10)->total())->toBe(3);
});

test('does not serve one cached page for another page or page size', function (): void {
    $users = User::factory()->count(6)->create();

    $action = new GetPaginatedUsers();
    $action->handle(page: 1, perPage: 2);

    expect(collect($action->handle(page: 2, perPage: 2)->items())->pluck('uuid')->all())
        ->toBe($users->slice(2, 2)->pluck('uuid')->values()->all())
        ->and($action->handle(page: 1, perPage: 3)->items())->toHaveCount(3);
});
