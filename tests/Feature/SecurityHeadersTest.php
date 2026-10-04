<?php

declare(strict_types=1);

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

test('production csp allows inline styles for vue :style bindings', function (): void {
    $response = $this->get('/');

    $csp = $response->headers->get('Content-Security-Policy');

    expect($csp)->toBeString()
        ->toMatch("/(?:^|;)\\s*style-src\\b[^;]*'unsafe-inline'/");
});

test('production csp does not leak dev-only sources', function (): void {
    // Test env is not "local", so Vite dev sources and unsafe-eval must be absent.
    $csp = $this->get('/')->headers->get('Content-Security-Policy');

    expect($csp)->not->toContain("'unsafe-eval'")
        ->and($csp)->not->toContain('localhost:5173')
        ->and($csp)->toContain('upgrade-insecure-requests');
});

test('csp is not applied to api paths', function (): void {
    $response = $this->getJson('/api/nonexistent-endpoint');

    expect($response->headers->get('Content-Security-Policy'))->toBeNull();
});

test('security headers are applied to public pages', function (): void {
    $response = $this->get('/');

    expect($response->headers->get('X-Content-Type-Options'))->toBe('nosniff')
        ->and($response->headers->get('X-Frame-Options'))->toBe('DENY');
});

test('csp is not applied to admin tool paths', function (string $uri): void {
    $response = new SecurityHeaders()->handle(
        Request::create($uri),
        fn (Request $request): Response => new Response('ok'),
    );

    expect($response->headers->get('Content-Security-Policy'))->toBeNull()
        ->and($response->headers->get('X-Frame-Options'))->toBe('DENY');
})->with([
    'activity log ui' => ['activitylog-ui/logs'],
    'api docs'        => ['docs/api'],
    'email previews'  => ['emails/failed-job'],
    'health'          => ['health'],
    'horizon'         => ['horizon/dashboard'],
    'laravel brain'   => ['_laravel-brain'],
    'log viewer'      => ['log-viewer'],
    'pulse'           => ['pulse'],
    'telescope'       => ['telescope/requests'],
    'web tinker'      => ['tinker'],
]);

test('csp is not applied to telescope under a custom path', function (): void {
    config(['telescope.path' => 'ops/telescope']);

    $response = new SecurityHeaders()->handle(
        Request::create('ops/telescope/requests'),
        fn (Request $request): Response => new Response('ok'),
    );

    expect($response->headers->get('Content-Security-Policy'))->toBeNull();
});

test('csp is applied to paths that only share a prefix with an admin tool', function (): void {
    $response = new SecurityHeaders()->handle(
        Request::create('healthy'),
        fn (Request $request): Response => new Response('ok'),
    );

    expect($response->headers->get('Content-Security-Policy'))->toBeString();
});
