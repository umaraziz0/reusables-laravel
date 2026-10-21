<?php

declare(strict_types=1);

use App\Enums\Role;
use App\Models\User;
use Spatie\Permission\Models\Role as RoleModel;

beforeEach(function (): void {
    RoleModel::query()->create(['name' => Role::SUPERADMIN->value, 'guard_name' => 'web']);
});

test('sidebar opens the folder that contains the current page', function (): void {
    $user = User::factory()->create();
    $user->assignRole(Role::SUPERADMIN->value);
    $this->actingAs($user);

    $page = visit(route('components.form.select-field', absolute: false));

    $page->assertSee('Input Group Field')
        ->assertNoJavaScriptErrors();
});

test('sidebar keeps folders closed when none of their pages is current', function (): void {
    $user = User::factory()->create();
    $user->assignRole(Role::SUPERADMIN->value);
    $this->actingAs($user);

    $page = visit(route('components.buttons', absolute: false));

    $page->assertDontSee('Input Group Field')
        ->assertNoJavaScriptErrors();
});
