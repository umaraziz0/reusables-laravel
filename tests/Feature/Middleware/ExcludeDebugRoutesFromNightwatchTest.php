<?php

declare(strict_types=1);

use App\Http\Middleware\ExcludeDebugRoutesFromNightwatch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Nightwatch\Facades\Nightwatch;

function passThroughMiddleware(string $uri): Symfony\Component\HttpFoundation\Response
{
    $middleware = new ExcludeDebugRoutesFromNightwatch();

    return $middleware->handle(
        Request::create($uri),
        fn (Request $request): Response => new Response('ok'),
    );
}

beforeEach(function (): void {
    Nightwatch::sample(1);
});

it('stops sampling for excluded dashboard paths', function (string $uri): void {
    passThroughMiddleware($uri);

    expect(Nightwatch::sampling())->toBeFalse();
})->with([
    'horizon root'          => ['horizon'],
    'horizon nested'        => ['horizon/dashboard'],
    'pulse'                 => ['pulse'],
    'activitylog-ui'        => ['activitylog-ui/logs'],
    'log-viewer'            => ['log-viewer'],
    'emails'                => ['emails/1'],
    'health'                => ['health'],
    'telescope from config' => ['telescope/requests'],
    'api docs'              => ['docs/api'],
    'web tinker'            => ['tinker'],
    'laravel brain'         => ['_laravel-brain'],
]);

it('stops sampling for telescope under a custom path', function (): void {
    config(['telescope.path' => 'ops/telescope']);

    passThroughMiddleware('ops/telescope/requests');

    expect(Nightwatch::sampling())->toBeFalse();
});

it('does not stop sampling for normal application paths', function (string $uri): void {
    passThroughMiddleware($uri);

    expect(Nightwatch::sampling())->toBeTrue();
})->with([
    'root'             => ['/'],
    'dashboard'        => ['dashboard'],
    'api endpoint'     => ['api/users'],
    'partial match'    => ['horizontal'],
    'health lookalike' => ['healthy'],
]);

it('always forwards the request to the next handler', function (): void {
    $response = passThroughMiddleware('horizon');

    expect($response->getContent())->toBe('ok');
});

it('skips an empty telescope path without excluding everything', function (): void {
    config(['telescope.path' => '']);

    passThroughMiddleware('/');

    expect(Nightwatch::sampling())->toBeTrue();
});
