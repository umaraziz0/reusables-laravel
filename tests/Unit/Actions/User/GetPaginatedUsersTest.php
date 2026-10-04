<?php

declare(strict_types=1);

use App\Actions\User\GetPaginatedUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

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

test('caches the result under a page and per page specific key', function (): void {
    User::factory()->count(3)->create();

    $action = new GetPaginatedUsers();
    $action->handle(page: 1, perPage: 10);

    $cacheKey = User::CACHE_TAG_LIST . ':paginated:page:1:per_page:10';

    $cached = Cache::tags(User::CACHE_TAG_LIST)->get($cacheKey);

    expect($cached)->not->toBeNull();
});

test('different page and per page combinations are cached separately', function (): void {
    User::factory()->count(20)->create();

    $action = new GetPaginatedUsers();
    $action->handle(page: 1, perPage: 10);
    $action->handle(page: 2, perPage: 10);

    $firstPageCacheKey  = User::CACHE_TAG_LIST . ':paginated:page:1:per_page:10';
    $secondPageCacheKey = User::CACHE_TAG_LIST . ':paginated:page:2:per_page:10';

    expect(Cache::tags(User::CACHE_TAG_LIST)->get($firstPageCacheKey))->not->toBeNull()
        ->and(Cache::tags(User::CACHE_TAG_LIST)->get($secondPageCacheKey))->not->toBeNull();
});
