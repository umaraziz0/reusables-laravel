<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Fortify\Features;

beforeEach(function (): void {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function (): void {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function (): void {
    $response = $this->post(route('register.store'), [
        'name'                  => 'Test User',
        'email'                 => 'test@example.com',
        'password'              => validTestPassword(),
        'password_confirmation' => validTestPassword(),
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not register with the email of a soft deleted user', function (): void {
    $deletedUser = User::factory()->trashed()->create();

    $response = $this->post(route('register.store'), [
        'name'                  => 'Test User',
        'email'                 => $deletedUser->email,
        'password'              => validTestPassword(),
        'password_confirmation' => validTestPassword(),
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors(['email' => 'The email has already been taken.']);
    $this->assertDatabaseCount('users', 1);
});
