<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function (): void {
    $this->privateApiUser = authenticatePrivateApi();
});

test('returns correct JSON structure with pagination', function (): void {
    $response = $this->getJson(route('api.users.index.paginated'));

    $response
        ->assertOk()
        ->assertJsonStructure([
            'success',
            'message',
            'data',
            'meta' => [
                'current_page',
                'last_page',
                'per_page',
                'total',
            ],
            'links',
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Users retrieved successfully',
        ])
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.uuid', $this->privateApiUser->uuid)
        ->assertJsonPath('meta.total', 1);
});

test('returns paginated users with correct resource structure', function (): void {
    $this->travelTo('2026-01-02 03:04:05');

    $users = User::factory()->count(15)->create();

    $response = $this->getJson(route('api.users.index.paginated'));

    $response->assertJsonCount(10, 'data');
    $response->assertJsonPath('meta.per_page', 10);
    $response->assertJsonPath('meta.total', 16);
    $response->assertJsonPath('meta.current_page', 1);

    $firstUser = $users->first();

    $response->assertJsonFragment([
        'uuid'       => $firstUser->uuid,
        'name'       => $firstUser->name,
        'email'      => $firstUser->email,
        'created_at' => '2026-01-02 03:04:05',
    ]);

    $data = $response->json('data.0');
    expect($data)->not->toHaveKeys(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes']);
});

test('orders paginated users by id ascending', function (): void {
    $this->privateApiUser->id = 40;
    $this->privateApiUser->save();

    $userWithId30     = User::factory()->make();
    $userWithId30->id = 30;
    $userWithId30->save();

    $userWithId10     = User::factory()->make();
    $userWithId10->id = 10;
    $userWithId10->save();

    $userWithId20     = User::factory()->make();
    $userWithId20->id = 20;
    $userWithId20->save();

    $response = $this->getJson(route('api.users.index.paginated'));

    $response
        ->assertOk()
        ->assertJsonPath('data.0.uuid', $userWithId10->uuid)
        ->assertJsonPath('data.1.uuid', $userWithId20->uuid)
        ->assertJsonPath('data.2.uuid', $userWithId30->uuid)
        ->assertJsonPath('data.3.uuid', $this->privateApiUser->uuid);
});

test('returns page two when the page query parameter is two', function (): void {
    $users = User::factory()->count(10)->create();

    $response = $this->getJson(route('api.users.index.paginated', ['page' => 2]));

    $response
        ->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Users retrieved successfully',
            'data'    => [[
                'uuid'  => $users->last()->uuid,
                'name'  => $users->last()->name,
                'email' => $users->last()->email,
            ]],
            'meta' => [
                'current_page' => 2,
                'per_page'     => 10,
                'total'        => 11,
            ],
        ])
        ->assertJsonCount(1, 'data');
});
