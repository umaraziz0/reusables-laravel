<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

describe('on non-PostgreSQL connections', function (): void {
    beforeEach(function (): void {
        config(['database.connections.search_sqlite' => [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]]);

        Schema::connection('search_sqlite')->create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('country_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });

        DB::connection('search_sqlite')->table('users')->insert([
            ['name' => 'Jane Searchable', 'email' => 'jane@example.com', 'country_code' => '+62', 'phone_number' => '81234567890'],
            ['name' => 'Other Person', 'email' => 'findme@example.com', 'country_code' => '+1', 'phone_number' => '81234567890'],
            ['name' => 'Discount Seeker', 'email' => '100%off@example.com', 'country_code' => null, 'phone_number' => null],
            ['name' => 'Bulk Buyer', 'email' => '1000off@example.com', 'country_code' => null, 'phone_number' => null],
        ]);
    });

    afterEach(function (): void {
        DB::purge('search_sqlite');
    });

    test('search scope matches name, email, phone and full international phone', function (string $searchTerm, array $expectedEmails): void {
        $results = User::on('search_sqlite')->filterByKeyword($searchTerm)->orderBy('id')->pluck('email')->all();

        expect($results)->toBe($expectedEmails);
    })->with([
        'name'                     => ['Searchable', ['jane@example.com']],
        'email'                    => ['findme@', ['findme@example.com']],
        'phone number'             => ['81234567890', ['jane@example.com', 'findme@example.com']],
        'full international phone' => ['+6281234567890', ['jane@example.com']],
        'escaped wildcard'         => ['100%', ['100%off@example.com']],
    ]);
});
