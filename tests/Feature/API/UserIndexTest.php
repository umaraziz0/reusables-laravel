<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function (): void {
    $this->privateApiUser = authenticatePrivateApi();
});

test('returns correct JSON structure', function (): void {
    $response = $this->getJson(route('api.users.index'));

    $response
        ->assertOk()
        ->assertJsonStructure([
            'success',
            'message',
            'data',
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Users retrieved successfully',
        ])
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.uuid', $this->privateApiUser->uuid);
});

test('returns all users with correct resource structure', function (): void {
    $this->travelTo('2026-01-02 03:04:05');

    $users = User::factory()->count(3)->create();

    $response = $this->getJson(route('api.users.index'));

    $response->assertJsonCount(4, 'data');

    foreach ($users as $user) {
        $response->assertJsonFragment([
            'uuid'       => $user->uuid,
            'name'       => $user->name,
            'email'      => $user->email,
            'created_at' => '2026-01-02 03:04:05',
        ]);
    }

    $data = $response->json('data.0');
    expect($data)->not->toHaveKeys(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes']);
});
