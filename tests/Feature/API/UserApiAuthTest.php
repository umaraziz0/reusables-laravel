<?php

declare(strict_types=1);

use App\Enums\Ability\UserAbility;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function (): void {
    config(['api.allowed_ips' => ['127.0.0.1']]);
});

test('denies unauthenticated requests', function (): void {
    $this->getJson(route('api.users.index'))
        ->assertUnauthorized();
});

test('denies authenticated requests without the users.view ability', function (): void {
    Sanctum::actingAs(User::factory()->create(), []);

    $this->getJson(route('api.users.index'))
        ->assertForbidden();
});

test('denies authenticated paginated requests without the users.view ability', function (): void {
    Sanctum::actingAs(User::factory()->create(), []);

    $this->getJson(route('api.users.index.paginated'))
        ->assertForbidden();
});

test('denies authenticated show requests without the users.view ability', function (): void {
    $user = User::factory()->create();

    Sanctum::actingAs(User::factory()->create(), []);

    $this->getJson(route('api.users.show', ['uuid' => $user->uuid]))
        ->assertForbidden();
});

test('denies authenticated requests without the users.create ability', function (): void {
    Sanctum::actingAs(User::factory()->create(), [UserAbility::View->value]);

    $this->postJson(route('api.users.store'), [
        'name'                  => 'Jane Doe',
        'email'                 => 'jane@example.com',
        'password'              => validTestPassword(),
        'password_confirmation' => validTestPassword(),
    ])->assertForbidden();
});
