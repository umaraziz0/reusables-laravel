<?php

declare(strict_types=1);

beforeEach(function (): void {
    authenticatePrivateApi();
});

test('api routes are rate limited per authenticated user', function (): void {
    for ($request = 0; $request < 60; $request++) {
        $this->getJson(route('api.users.index'))
            ->assertSuccessful();
    }

    $this->getJson(route('api.users.index'))
        ->assertTooManyRequests()
        ->assertHeader('X-RateLimit-Limit', '60')
        ->assertHeader('X-RateLimit-Remaining', '0')
        ->assertHeader('Retry-After');

    authenticatePrivateApi();

    $this->getJson(route('api.users.index'))
        ->assertSuccessful();
});

test('api routes include rate limit headers', function (): void {
    $response = $this->getJson(route('api.users.index'));

    $response->assertSuccessful();
    $response->assertHeader('X-RateLimit-Limit', '60');
});
