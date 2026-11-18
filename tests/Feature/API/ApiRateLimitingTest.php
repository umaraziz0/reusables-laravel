<?php

declare(strict_types=1);

beforeEach(function (): void {
    authenticatePrivateApi();
});

test('api routes are rate limited per authenticated user', function (): void {
    $this->getJson(route('api.users.index'))
        ->assertSuccessful()
        ->assertHeader('X-RateLimit-Limit', '60')
        ->assertHeader('X-RateLimit-Remaining', '59');

    for ($request = 1; $request < 60; $request++) {
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
