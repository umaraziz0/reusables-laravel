<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Starter\InitializeProject;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Laravel\Prompts\text;

#[Signature('starter:init
    {--name= : Project slug in kebab-case, e.g. acme-portal}
    {--repository= : GitHub repository as owner/name}
    {--description= : composer.json description}
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

        $description = $this->stringOption('description') ?? (text(label: 'Description', hint: 'Leave empty to keep the current one.') ?: null);

        $error = $this->validateSlug($slug) ?? $this->validateRepository($repository);

        if ($error !== null) {
            $this->components->error($error);

            return self::INVALID;
        }

        $changedFiles = $initializeProject->handle(base_path(), $slug, $repository, $description);

        $this->components->info("Initialized {$slug}. Changed files:");
        $this->components->bulletList($changedFiles);

        $this->components->twoColumnDetail('<options=bold>Next steps</>');
        $this->components->bulletList([
            "Point git at the new repository: git remote set-url origin git@github.com:{$repository}.git",
            "Create the databases: {$this->database($slug)} and {$this->database($slug)}_testing",
            'Create the triage labels: bash .github/scripts/setup-labels.sh',
            'Review the diff, then commit: git commit -am "chore: initialize project from starter kit"',
        ]);

        return self::SUCCESS;
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
        return preg_match(self::REPOSITORY_PATTERN, $repository) === 1
            ? null
            : 'The repository must look like owner/name.';
    }

    private function database(string $slug): string
    {
        return Str::replace('-', '_', $slug);
    }
}
