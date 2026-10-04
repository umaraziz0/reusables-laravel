<?php

declare(strict_types=1);

use App\Enums\Ability\UserAbility;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;

beforeEach(function (): void {
    Route::middleware(['ip_whitelist', 'auth:sanctum'])
        ->get('/api/private-test', fn () => response()->json(['success' => true]));

    config(['api.allowed_ips' => ['203.0.113.10']]);

    $this->app->instance('env', 'testing');

    Sanctum::actingAs(User::factory()->create(), ['*']);
});

test('allows requests from whitelisted ips', function (): void {
    $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
        ->getJson('/api/private-test')
        ->assertSuccessful()
        ->assertJson(['success' => true]);
});

test('denies requests from non-whitelisted ips', function (): void {
    $log = Log::spy();

    $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.50'])
        ->getJson('/api/private-test')
        ->assertForbidden()
        ->assertJson([
            'success' => false,
            'message' => 'Forbidden',
            'data'    => null,
        ]);

    $log->shouldHaveReceived('log')
        ->once()
        ->with('warning', 'API - IP Denied - ip denied', Mockery::on(fn (array $context): bool => ($context['security_data'] ?? null) === [
            'ip'                 => '198.51.100.50',
            'path'               => 'api/private-test',
            'method'             => 'GET',
            'is_allowed_ips_set' => true,
        ]));
});

test('denies all requests when whitelist is empty in non-local environments', function (): void {
    config(['api.allowed_ips' => []]);

    $log = Log::spy();

    $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
        ->getJson('/api/private-test')
        ->assertForbidden();

    $log->shouldHaveReceived('log')
        ->once()
        ->with('warning', 'API - IP Denied - ip denied', Mockery::on(fn (array $context): bool => ($context['security_data'] ?? null) === [
            'ip'                 => '203.0.113.10',
            'path'               => 'api/private-test',
            'method'             => 'GET',
            'is_allowed_ips_set' => false,
        ]));
});

test('bypasses whitelist in local environment', function (): void {
    $this->app->instance('env', 'local');

    $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.50'])
        ->getJson('/api/private-test')
        ->assertSuccessful()
        ->assertJson(['success' => true]);
});

test('allows requests matching a cidr range', function (): void {
    config(['api.allowed_ips' => ['10.0.0.0/8']]);

    $this->withServerVariables(['REMOTE_ADDR' => '10.1.2.3'])
        ->getJson('/api/private-test')
        ->assertSuccessful()
        ->assertJson(['success' => true]);
});

test('denies private user api routes from non-whitelisted ips', function (): void {
    Sanctum::actingAs(User::factory()->create(), [UserAbility::View->value]);

    $log = Log::spy();

    $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.50'])
        ->getJson(route('api.users.index'))
        ->assertForbidden()
        ->assertJson([
            'success' => false,
            'message' => 'Forbidden',
            'data'    => null,
        ]);

    $log->shouldHaveReceived('log')
        ->once()
        ->with('warning', 'API - IP Denied - ip denied', Mockery::on(fn (array $context): bool => ($context['security_data'] ?? null) === [
            'ip'                 => '198.51.100.50',
            'path'               => 'api/users',
            'method'             => 'GET',
            'is_allowed_ips_set' => true,
        ]));
});
