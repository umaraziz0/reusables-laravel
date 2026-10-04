<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->use(RefreshDatabase::class);

test('search scope finds users by name', function (): void {
    $user = User::factory()->create(['name' => 'Jane Searchable']);

    User::factory()->create(['name' => 'Other Person']);

    $results = User::query()->filterByKeyword('searchable')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()?->is($user))->toBeTrue();
});

test('search scope finds users by email', function (): void {
    $user = User::factory()->create(['email' => 'findme@example.com']);

    User::factory()->create(['email' => 'hidden@example.com']);

    $results = User::query()->filterByKeyword('findme@')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()?->is($user))->toBeTrue();
});

test('search scope finds users by phone number', function (): void {
    $user = User::factory()->create([
        'country_code' => '+62',
        'phone_number' => '81234567890',
    ]);

    User::factory()->create([
        'country_code' => '+1',
        'phone_number' => '5551234567',
    ]);

    $results = User::query()->filterByKeyword('81234567890')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()?->is($user))->toBeTrue();
});

test('search scope finds users by full international phone number', function (): void {
    $user = User::factory()->create([
        'country_code' => '+62',
        'phone_number' => '81234567890',
    ]);

    User::factory()->create([
        'country_code' => '+1',
        'phone_number' => '81234567890',
    ]);

    $results = User::query()->filterByKeyword('+6281234567890')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()?->is($user))->toBeTrue();
});

test('search scope is case insensitive for names', function (): void {
    $user = User::factory()->create(['name' => 'Jane Searchable']);

    $results = User::query()->filterByKeyword('SEARCHABLE')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()?->is($user))->toBeTrue();
});

test('search scope ignores blank search terms', function (?string $searchTerm): void {
    User::factory()->count(2)->create();

    expect(User::query()->filterByKeyword($searchTerm)->count())->toBe(2);
})->with([
    'null'       => null,
    'empty'      => '',
    'whitespace' => '   ',
]);
