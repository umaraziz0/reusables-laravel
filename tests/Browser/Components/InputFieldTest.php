<?php

declare(strict_types=1);

use App\Enums\Role;
use App\Models\User;
use Spatie\Permission\Models\Role as RoleModel;

test('email input suggests and applies a corrected address on the preview page', function (): void {
    RoleModel::query()->create(['name' => Role::SUPERADMIN->value, 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole(Role::SUPERADMIN->value);
    $this->actingAs($user);

    $page = visit(route('components.form.input-field', absolute: false))
        ->type('#email', 'jane@gmial.com')
        ->keys('#email', 'Tab')
        ->assertSee('Did you mean')
        ->click('[role="status"] button');

    $page->assertValue('#email', 'jane@gmail.com')
        ->assertDontSee('Did you mean')
        ->assertNoJavaScriptErrors();
});
