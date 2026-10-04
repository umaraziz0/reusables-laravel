<?php

declare(strict_types=1);

use App\Enums\Role;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Opcodes\LogViewer\Facades\LogViewer;
use Spatie\Permission\Models\Role as RoleModel;

beforeEach(function (): void {
    RoleModel::query()->firstOrCreate(
        ['name' => Role::SUPERADMIN->value, 'guard_name' => 'web'],
    );
});

test('guests cannot view the admin tools outside local', function (): void {
    expect(Gate::allows('viewPulse'))->toBeFalse()
        ->and(Gate::allows('viewActivityLogUi'))->toBeFalse()
        ->and(Gate::allows('viewHorizon'))->toBeFalse()
        ->and(Gate::allows('viewTelescope'))->toBeFalse()
        ->and(fn () => LogViewer::auth())->toThrow(AuthorizationException::class);
});

test('admin tool routes behind login redirect guests to the login page', function (string $routeName): void {
    $response = $this->get(route($routeName));

    $response->assertRedirect(route('login', absolute: false));
})->with([
    'Activity Log UI' => 'activitylog-ui.dashboard',
    'Health'          => 'health',
    'Email previews'  => 'emails.failed-job',
]);

test('guests can view the admin tools locally', function (): void {
    app()->instance('env', 'local');

    expect(Gate::allows('viewPulse'))->toBeTrue()
        ->and(Gate::allows('viewActivityLogUi'))->toBeTrue()
        ->and(Gate::allows('viewHorizon'))->toBeTrue()
        ->and(Gate::allows('viewTelescope'))->toBeTrue()
        ->and(Gate::allows('viewLogViewer'))->toBeTrue();
});

test('admin tool routes forbid authenticated users without the superadmin role', function (string $routeName): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route($routeName));

    $response->assertForbidden();
})->with('admin tool routes');

test('admin tool routes allow users with the superadmin role', function (string $routeName): void {
    $user = User::factory()->create();
    $user->assignRole(Role::SUPERADMIN->value);

    $response = $this->actingAs($user)->get(route($routeName));

    $response->assertSuccessful();
})->with('admin tool routes');

dataset('admin tool routes', [
    'Pulse'           => 'pulse',
    'LogViewer'       => 'log-viewer.index',
    'Activity Log UI' => 'activitylog-ui.dashboard',
    'Horizon'         => 'horizon.index',
    'Telescope'       => 'telescope',
    'Health'          => 'health',
    'Email previews'  => 'emails.failed-job',
]);
