<?php

declare(strict_types=1);

use App\Models\User;

beforeEach(function (): void {
    $this->privateApiUser = authenticatePrivateApi();
});

test('returns 404 for a malformed UUID', function (): void {
    $this->getJson(route('api.users.show', ['uuid' => 'not-a-uuid']))
        ->assertNotFound();
});

test('returns 404 when user does not exist', function (): void {
    $response = $this->getJson(route('api.users.show', ['uuid' => '00000000-0000-0000-0000-000000000000']));

    $response->assertNotFound();
    $response->assertJson([
        'success' => false,
        'message' => 'User not found',
    ]);
});

test('returns correct JSON structure', function (): void {
    $this->travelTo('2026-01-02 03:04:05');

    $user = User::factory()->create();

    $response = $this->getJson(route('api.users.show', ['uuid' => $user->uuid]));

    $response
        ->assertOk()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'uuid',
                'name',
                'email',
                'created_at',
            ],
        ])
        ->assertJson([
            'success' => true,
            'message' => 'User retrieved successfully',
            'data'    => [
                'uuid'       => $user->uuid,
                'name'       => $user->name,
                'email'      => $user->email,
                'created_at' => '2026-01-02 03:04:05',
            ],
        ]);

    $data = $response->json('data');
    expect($data)->not->toHaveKeys(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes']);
});
