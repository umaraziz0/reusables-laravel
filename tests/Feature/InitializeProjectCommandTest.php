<?php

declare(strict_types=1);

use App\Actions\Starter\InitializeProject;
use Illuminate\Support\Facades\File;
use Illuminate\Testing\PendingCommand;
use Mockery\MockInterface;

/**
 * @param  array<string, string|bool>  $parameters
 */
function starterInit(array $parameters = []): PendingCommand
{
    $command = test()->artisan('starter:init', $parameters);

    throw_unless($command instanceof PendingCommand, LogicException::class, 'Console output must be mocked to assert on starter:init.');

    return $command;
}

/**
 * Points base_path() at a temporary project, so the "already initialized" guard reads
 * its composer.json instead of the real one (which `starter:init` itself renames).
 */
function useTemporaryProject(string $composerName): string
{
    $projectPath = storage_path('framework/testing/starter-init-' . uniqid());
    File::ensureDirectoryExists($projectPath);
    File::put("{$projectPath}/composer.json", json_encode(['name' => $composerName], JSON_THROW_ON_ERROR));
    app()->setBasePath($projectPath);

    return $projectPath;
}

beforeEach(function (): void {
    $this->projectPath = useTemporaryProject(InitializeProject::TEMPLATE_REPOSITORY);
});

afterEach(function (): void {
    File::deleteDirectory($this->projectPath);
});

test('it initializes the project from the given options', function (): void {
    $this->mock(InitializeProject::class, function (MockInterface $mock): void {
        $mock->expects('handle')
            ->with(base_path(), 'acme-portal', 'acme/portal', 'Client portal')
            ->andReturn(['composer.json']);
    });

    starterInit([
        '--name'        => 'acme-portal',
        '--repository'  => 'acme/portal',
        '--description' => 'Client portal',
    ])
        ->expectsOutputToContain('Initialized acme-portal')
        ->expectsOutputToContain('acme_portal_testing')
        ->assertSuccessful();
});

test('it prompts for missing values', function (): void {
    $this->mock(InitializeProject::class, function (MockInterface $mock): void {
        $mock->expects('handle')
            ->with(base_path(), 'acme-portal', 'umaraziz0/acme-portal', null)
            ->andReturn([]);
    });

    starterInit()
        ->expectsQuestion('Project slug', 'acme-portal')
        ->expectsQuestion('GitHub repository', 'umaraziz0/acme-portal')
        ->expectsQuestion('Description', '')
        ->assertSuccessful();
});

test('it rejects invalid options without touching any file', function (array $options): void {
    $this->mock(InitializeProject::class, function (MockInterface $mock): void {
        $mock->shouldNotReceive('handle');
    });

    starterInit([...$options, '--description' => 'x'])->assertExitCode(2);
})->with([
    'slug with uppercase'   => [['--name' => 'Acme Portal', '--repository' => 'acme/portal']],
    'repository not a path' => [['--name' => 'acme-portal', '--repository' => 'acme']],
]);

test('it refuses to run on an already initialized project unless forced', function (): void {
    File::put("{$this->projectPath}/composer.json", json_encode(['name' => 'acme/portal'], JSON_THROW_ON_ERROR));

    $this->mock(InitializeProject::class, function (MockInterface $mock): void {
        $mock->expects('handle')->once()->andReturn([]);
    });

    $options = ['--name' => 'acme-portal', '--repository' => 'acme/portal', '--description' => 'x'];

    starterInit($options)
        ->expectsOutputToContain('already initialized')
        ->assertFailed();

    starterInit([...$options, '--force' => true])->assertSuccessful();

});
