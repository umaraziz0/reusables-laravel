<?php

declare(strict_types=1);

use App\Actions\User\CreateUser;
use Illuminate\Support\Facades\Hash;

test('creates a user with the given data', function (): void {
    $data = [
        'name'         => 'Jane Doe',
        'email'        => 'jane@example.com',
        'password'     => 'secret-password',
        'country_code' => '+1',
        'phone_number' => '5551234567',
    ];

    $action = new CreateUser();
    $user   = $action->handle($data);

    expect($user->name)->toBe('Jane Doe')
        ->and($user->email)->toBe('jane@example.com')
        ->and($user->country_code)->toBe('+1')
        ->and($user->phone_number)->toBe('5551234567')
        ->and(Hash::check('secret-password', $user->password))->toBeTrue();

    $this->assertDatabaseHas('users', [
        'email'        => 'jane@example.com',
        'country_code' => '+1',
        'phone_number' => '5551234567',
    ]);
});

test('defaults country_code and phone_number to null when not provided', function (): void {
    $data = [
        'name'     => 'John Doe',
        'email'    => 'john@example.com',
        'password' => 'secret-password',
    ];

    $action = new CreateUser();
    $user   = $action->handle($data);

    expect($user->country_code)->toBeNull()
        ->and($user->phone_number)->toBeNull();

    $this->assertDatabaseHas('users', [
        'email'        => 'john@example.com',
        'country_code' => null,
        'phone_number' => null,
    ]);
});
