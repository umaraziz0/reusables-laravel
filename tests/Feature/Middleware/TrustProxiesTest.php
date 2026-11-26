<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

const CLOUDFLARE_EDGE_IP = '162.158.10.20';

/**
 * Loads config/trustedproxy.php as if TRUSTED_PROXIES were set to the given value.
 */
function trustProxiesFromEnv(string $trustedProxies): void
{
    $_SERVER['TRUSTED_PROXIES'] = $trustedProxies;

    config(['trustedproxy' => require config_path('trustedproxy.php')]);
}

afterEach(function (): void {
    unset($_SERVER['TRUSTED_PROXIES']);
});

beforeEach(function (): void {
    Route::get('/_trust-proxies-probe', fn (Request $request): array => [
        'ip'     => $request->ip(),
        'secure' => $request->isSecure(),
        'host'   => $request->getHost(),
    ]);
});

test('behind cloudflare, the client ip comes from x-forwarded-for', function (): void {
    trustProxiesFromEnv('cloudflare');

    $this->withServerVariables(['REMOTE_ADDR' => CLOUDFLARE_EDGE_IP])
        ->withHeaders(['X-Forwarded-For' => '203.0.113.7', 'X-Forwarded-Proto' => 'https'])
        ->getJson('/_trust-proxies-probe')
        ->assertJson(['ip' => '203.0.113.7', 'secure' => true]);
});

test('behind cloudflare, a request from outside cloudflare cannot spoof forwarded headers', function (): void {
    trustProxiesFromEnv('cloudflare');

    $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.50'])
        ->withHeaders(['X-Forwarded-For' => '203.0.113.7', 'X-Forwarded-Proto' => 'https'])
        ->getJson('/_trust-proxies-probe')
        ->assertJson(['ip' => '198.51.100.50', 'secure' => false]);
});

test('behind cloudflare, x-forwarded-host is ignored', function (): void {
    trustProxiesFromEnv('cloudflare');

    $this->withServerVariables(['REMOTE_ADDR' => CLOUDFLARE_EDGE_IP])
        ->withHeaders(['X-Forwarded-Host' => 'attacker.example'])
        ->getJson('/_trust-proxies-probe')
        ->assertJsonMissing(['host' => 'attacker.example']);
});

test('with direct connections, forwarded headers are ignored', function (): void {
    trustProxiesFromEnv('');

    $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.50'])
        ->withHeaders(['X-Forwarded-For' => '203.0.113.7', 'X-Forwarded-Proto' => 'https'])
        ->getJson('/_trust-proxies-probe')
        ->assertJson(['ip' => '198.51.100.50', 'secure' => false]);
});

test('cloudflare and a local reverse proxy can be trusted together', function (): void {
    trustProxiesFromEnv('cloudflare, 127.0.0.1');

    $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
        ->withHeaders(['X-Forwarded-For' => '203.0.113.7, ' . CLOUDFLARE_EDGE_IP])
        ->getJson('/_trust-proxies-probe')
        ->assertJson(['ip' => '203.0.113.7']);
});

test('a wildcard is not supported and trusts nobody', function (): void {
    trustProxiesFromEnv('*');

    $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.50'])
        ->withHeaders(['X-Forwarded-For' => '203.0.113.7'])
        ->getJson('/_trust-proxies-probe')
        ->assertJson(['ip' => '198.51.100.50']);
});
