<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Starter\InitializeProject;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

use function Laravel\Prompts\text;

#[Signature('starter:init
    {--name= : Project slug in kebab-case, e.g. acme-portal}
    {--repository= : GitHub repository as owner/name}
    {--description= : composer.json description (required: the template\'s does not describe your project)}
    {--force : Run even if the project was already initialized}')]
#[Description('Rename this starter kit clone into a new project and reset its version and changelog')]
class InitializeProjectCommand extends Command
{
    private const string SLUG_PATTERN = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

    private const string REPOSITORY_PATTERN = '/^[\w.-]+\/[\w.-]+$/';

    public function handle(InitializeProject $initializeProject): int
    {
        if (! $this->option('force') && ! $this->isTemplate()) {
            $this->components->error('This project is already initialized. Pass --force to run it again.');

            return self::FAILURE;
        }

        $missingOptions = array_filter(['name', 'description'], fn (string $option): bool => $this->stringOption($option) === null);

        if (! $this->input->isInteractive() && $missingOptions !== []) {
            $this->components->error('Pass ' . implode(' and ', array_map(fn (string $option): string => "--{$option}", $missingOptions)) . ' when running non-interactively.');

            return self::INVALID;
        }

        $slug = $this->stringOption('name') ?? text(
            label: 'Project slug',
            placeholder: 'acme-portal',
            required: true,
            validate: fn (string $value): ?string => $this->validateSlug($value),
            hint: 'Used for APP_NAME, the Herd site, package names and (with underscores) database names.',
        );

        $repository = $this->stringOption('repository') ?? text(
            label: 'GitHub repository',
            default: Str::before(InitializeProject::TEMPLATE_REPOSITORY, '/') . "/{$slug}",
            required: true,
            validate: fn (string $value): ?string => $this->validateRepository($value),
        );

        $description = $this->stringOption('description') ?? text(
            label: 'Description',
            placeholder: 'Event ticketing platform for Acme',
            required: true,
            hint: 'Replaces the template description in composer.json.',
        );

        $error = $this->validateSlug($slug) ?? $this->validateRepository($repository);

        if ($error !== null) {
            $this->components->error($error);

            return self::INVALID;
        }

        $changedFiles = $initializeProject->handle(base_path(), $slug, $repository, $description);

        $this->components->info("Initialized {$slug}. Changed files:");
        $this->components->bulletList($changedFiles);

        $this->refreshComposerLockHash();

        $this->components->twoColumnDetail('<options=bold>Next steps</>');
        $this->components->bulletList([
            "Create the databases: {$this->database($slug)} and {$this->database($slug)}_testing",
            'Install and migrate: composer run setup',
            "Point git at the new repository: git remote rename origin template && git remote add origin git@github.com:{$repository}.git",
            'Create the triage labels: bash .github/scripts/setup-labels.sh',
            'Review the diff, then commit: git commit -am "chore: initialize project from starter kit"',
        ]);

        return self::SUCCESS;
    }

    /**
     * composer.lock's content hash covers composer.json's name, version and description,
     * so Composer reports the lock as outdated until it is refreshed. Package versions stay as they are.
     */
    private function refreshComposerLockHash(): void
    {
        $command = ['composer', 'update', '--lock', '--no-install', '--no-scripts', '--no-interaction'];

        $refreshed = false;

        $this->components->task(
            'Refreshing the composer.lock content hash',
            function () use ($command, &$refreshed): bool {
                $refreshed = Process::path(base_path())->timeout(300)->run($command)->successful();

                return $refreshed;
            },
        );

        if (! $refreshed) {
            $this->components->warn('Could not refresh composer.lock. Run: ' . implode(' ', $command));
        }
    }

    private function isTemplate(): bool
    {
        $composer = File::json(base_path('composer.json'));

        return ($composer['name'] ?? null) === InitializeProject::TEMPLATE_REPOSITORY;
    }

    private function stringOption(string $name): ?string
    {
        $value = $this->option($name);

        return is_string($value) && $value !== '' ? $value : null;
    }

    private function validateSlug(string $slug): ?string
    {
        return preg_match(self::SLUG_PATTERN, $slug) === 1
            ? null
            : 'The project slug must be kebab-case: lowercase letters, numbers and single dashes.';
    }

    private function validateRepository(string $repository): ?string
    {
        if (preg_match(self::REPOSITORY_PATTERN, $repository) !== 1) {
            return 'The repository must look like owner/name.';
        }

        if (preg_match(InitializeProject::COMPOSER_NAME_PATTERN, Str::lower($repository)) !== 1) {
            return 'The repository, lowercased, must also be a valid Composer package name (letters, numbers and single . _ - separators).';
        }

        return null;
    }

    private function database(string $slug): string
    {
        return Str::replace('-', '_', $slug);
    }
}
