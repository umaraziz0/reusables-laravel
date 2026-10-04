<?php

declare(strict_types=1);

namespace Tests\Feature\EmailPreview;

use App\Enums\Role;
use App\Models\User;
use Spatie\Permission\Models\Role as RoleModel;

beforeEach(function (): void {
    RoleModel::query()->firstOrCreate(
        ['name' => Role::SUPERADMIN->value, 'guard_name' => 'web'],
    );
});

test('failed job email preview returns HTML with expected content', function (): void {
    $user = User::factory()->create();
    $user->assignRole(Role::SUPERADMIN->value);

    $response = $this->actingAs($user)->get(route('emails.failed-job'));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/html; charset=UTF-8');
    $response->assertSee('Queue Job Failed');
});
