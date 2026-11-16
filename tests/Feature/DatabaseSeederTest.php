<?php

declare(strict_types=1);

use App\Enums\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Spatie\Permission\Models\Role as RoleModel;

test('seeding creates the roles and a superadmin who can open the admin tools', function (): void {
    $this->seed(DatabaseSeeder::class);

    $superadmin = User::query()->where('email', DatabaseSeeder::SUPERADMIN_EMAIL)->sole();

    expect(RoleModel::query()->pluck('name')->all())->toEqualCanonicalizing(Role::values())
        ->and($superadmin->hasRole(Role::SUPERADMIN->value))->toBeTrue()
        ->and(User::query()->where('email', DatabaseSeeder::USER_EMAIL)->sole()->roles)->toBeEmpty();

    $this->actingAs($superadmin)->get(route('health'))->assertOk();
});

test('seeding twice does not duplicate users', function (): void {
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    expect(User::query()->count())->toBe(2);
});

test('seeding outside local and testing creates roles but no demo users', function (string $environment): void {
    app()->instance('env', $environment);

    // Invoked directly: `db:seed` asks for confirmation in production.
    resolve(DatabaseSeeder::class)->setContainer(app())->__invoke();

    expect(RoleModel::query()->count())->toBe(count(Role::cases()))
        ->and(User::query()->count())->toBe(0);
})->with(['production', 'staging', 'dev']);
