<?php

declare(strict_types=1);

use App\Enums\Role;
use App\Models\User;
use Spatie\Permission\Models\Role as RoleModel;

beforeEach(function (): void {
    RoleModel::query()->firstOrCreate(
        ['name' => Role::SUPERADMIN->value, 'guard_name' => 'web'],
    );
});

test('component preview index requires authentication', function (): void {
    $response = $this->get(route('components.index'));

    $response->assertRedirect(route('login', absolute: false));
});

test('component preview index requires superadmin role', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('components.index'));

    $response->assertForbidden();
});

test('component preview index redirects to buttons for superadmin', function (): void {
    $user = User::factory()->create();
    $user->assignRole(Role::SUPERADMIN->value);

    $response = $this->actingAs($user)->get(route('components.index'));

    $response->assertRedirect(route('components.buttons'));
});

test('component preview pages require authentication', function (string $routeName): void {
    $response = $this->get(route($routeName));

    $response->assertRedirect(route('login', absolute: false));
})->with([
    'alert'            => 'components.alert',
    'badges'           => 'components.badges',
    'buttons'          => 'components.buttons',
    'collapsible'      => 'components.collapsible',
    'form basic input' => 'components.form.basic-input',
]);

test('component preview pages require superadmin role', function (string $routeName): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route($routeName));

    $response->assertForbidden();
})->with([
    'alert'            => 'components.alert',
    'badges'           => 'components.badges',
    'buttons'          => 'components.buttons',
    'collapsible'      => 'components.collapsible',
    'form basic input' => 'components.form.basic-input',
]);

test('superadmin can view component preview pages', function (string $routeName): void {
    $user = User::factory()->create();
    $user->assignRole(Role::SUPERADMIN->value);

    $response = $this->actingAs($user)->get(route($routeName));

    $response->assertSuccessful();
})->with([
    'alert'            => 'components.alert',
    'badges'           => 'components.badges',
    'buttons'          => 'components.buttons',
    'collapsible'      => 'components.collapsible',
    'form basic input' => 'components.form.basic-input',
]);
