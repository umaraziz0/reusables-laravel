<?php

declare(strict_types=1);

use App\Enums\Role;
use App\Models\User;
use Spatie\Permission\Models\Role as RoleModel;

test('search select field opens a large list at the selected item and selects a filtered match', function (): void {
    RoleModel::query()->create(['name' => Role::SUPERADMIN->value, 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole(Role::SUPERADMIN->value);
    $this->actingAs($user);

    $page = visit(route('components.form.search-select-field', absolute: false))
        ->click('#item')
        ->assertVisible('[role="option"][data-state="checked"]')
        ->assertSeeIn('[role="option"][data-state="checked"]', 'Item 004201');

    expect($page->script('document.querySelectorAll("[role=option]").length'))->toBeLessThan(100);

    $page->type('[role="combobox"]', '000999')
        ->keys('[role="combobox"]', 'Enter')
        ->assertSeeIn('#item', 'Item 000999')
        ->assertNoJavaScriptErrors();
});
