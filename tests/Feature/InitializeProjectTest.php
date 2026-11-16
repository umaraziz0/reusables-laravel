<?php

declare(strict_types=1);

use App\Actions\Starter\InitializeProject;
use Illuminate\Support\Facades\File;

/**
 * Copies the template's real files into a temporary project so the action runs on them.
 */
function temporaryProjectCopy(): string
{
    $projectPath = storage_path('framework/testing/initialize-project-' . uniqid());

    $files = [
        '.ai/guidelines/agent-skills.blade.php',
        '.betterleaks.toml',
        '.env.example',
        '.env.testing',
        '.github/workflows/ci.yml',
        '.github/workflows/tia-baseline.yml',
        '.release-please-manifest.json',
        'AGENTS.md',
        'CHANGELOG.md',
        'README.md',
        'composer.json',
        'herd.yml',
        'package.json',
        'phpunit.xml',
        'release-please-config.json',
    ];

    foreach ($files as $file) {
        File::ensureDirectoryExists(dirname("{$projectPath}/{$file}"));
        File::copy(base_path($file), "{$projectPath}/{$file}");
    }

    return $projectPath;
}

beforeEach(function (): void {
    $this->projectPath = temporaryProjectCopy();
});

afterEach(function (): void {
    File::deleteDirectory($this->projectPath);
});

test('it renames every reference to the template', function (): void {
    $changedFiles = resolve(InitializeProject::class)->handle($this->projectPath, 'acme-portal', 'acme/portal');

    foreach (File::allFiles($this->projectPath, hidden: true) as $file) {
        expect($file->getContents())
            ->not->toContain('reusables', $file->getRelativePathname())
            ->not->toContain('Reusables', $file->getRelativePathname());
    }

    expect($changedFiles)->toContain('composer.json', 'phpunit.xml', '.github/workflows/ci.yml', 'AGENTS.md')
        ->and(File::json("{$this->projectPath}/composer.json")['name'])->toBe('acme/portal')
        ->and(File::get("{$this->projectPath}/.env.example"))->toContain('APP_NAME=acme-portal', 'DB_DATABASE=acme_portal')
        ->and(File::get("{$this->projectPath}/phpunit.xml"))->toContain('value="acme_portal_testing"')
        ->and(File::get("{$this->projectPath}/README.md"))->toStartWith('# Acme Portal');
});

test('it resets the version and changelog', function (): void {
    resolve(InitializeProject::class)->handle($this->projectPath, 'acme-portal', 'acme/portal');

    expect(File::json("{$this->projectPath}/composer.json")['version'])->toBe(InitializeProject::INITIAL_VERSION)
        ->and(File::json("{$this->projectPath}/package.json")['version'])->toBe(InitializeProject::INITIAL_VERSION)
        ->and(File::json("{$this->projectPath}/.release-please-manifest.json"))->toBe(['.' => InitializeProject::INITIAL_VERSION])
        ->and(File::get("{$this->projectPath}/CHANGELOG.md"))->toBe("# Changelog\n");
});

test('it sets the description and keeps composer.json valid json', function (): void {
    $description = 'Client portal for "Acme" $1 \\ ünïcode';

    resolve(InitializeProject::class)->handle($this->projectPath, 'acme-portal', 'acme/portal', $description);

    expect(File::json("{$this->projectPath}/composer.json")['description'])->toBe($description);
});

test('it also renames a local .env when one exists', function (): void {
    File::copy(base_path('.env.example'), "{$this->projectPath}/.env");

    $changedFiles = resolve(InitializeProject::class)->handle($this->projectPath, 'acme-portal', 'acme/portal');

    expect($changedFiles)->toContain('.env')
        ->and(File::get("{$this->projectPath}/.env"))->toContain('APP_URL=http://acme-portal.test');
});

test('running it again changes nothing', function (): void {
    resolve(InitializeProject::class)->handle($this->projectPath, 'acme-portal', 'acme/portal');

    expect(resolve(InitializeProject::class)->handle($this->projectPath, 'acme-portal', 'acme/portal'))->toBeEmpty();
});
