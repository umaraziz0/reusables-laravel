<?php

declare(strict_types=1);

namespace App\Actions\Starter;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Turns a fresh clone of the starter kit into a new project: renames the
 * template's identifiers, resets the version to 0.1.0 and empties the changelog.
 */
class InitializeProject
{
    public const string TEMPLATE_REPOSITORY = 'umaraziz0/reusables-laravel';

    public const string INITIAL_VERSION = '0.1.0';

    /**
     * Composer's package name rule. GitHub allows uppercase, so the repository is
     * lowercased for composer.json and must still match this afterwards.
     */
    public const string COMPOSER_NAME_PATTERN = '{^[a-z0-9]([_.-]?[a-z0-9]+)*/[a-z0-9](([_.]|-{1,2})?[a-z0-9]+)*$}';

    /**
     * Files that mention the template's name, relative to the project root.
     * `.env` is included when it exists, since `composer run setup` copies it early.
     */
    public const array RENAMED_FILES = [
        '.ai/guidelines/agent-skills.blade.php',
        '.betterleaks.toml',
        '.env',
        '.env.example',
        '.env.testing',
        '.github/workflows/ci.yml',
        '.github/workflows/tia-baseline.yml',
        'AGENTS.md',
        'README.md',
        'composer.json',
        'herd.yml',
        'phpunit.xml',
        'release-please-config.json',
    ];

    private const string TEMPLATE_SLUG = 'reusables-laravel';

    private const string TEMPLATE_DATABASE = 'reusables_laravel';

    private const string TEMPLATE_TITLE = 'Reusables Laravel';

    private const string TEMPLATE_README_HEADING = '# Laravel & Vue Reusables';

    /**
     * @return list<string> the files that changed, relative to the project root
     */
    public function handle(string $basePath, string $slug, string $repository, ?string $description = null): array
    {
        $title = Str::headline($slug);

        $renames = [
            '"name": "' . self::TEMPLATE_REPOSITORY . '"' => '"name": "' . Str::lower($repository) . '"',
            self::TEMPLATE_REPOSITORY                     => $repository,
            self::TEMPLATE_README_HEADING                 => "# {$title}",
            self::TEMPLATE_SLUG                           => $slug,
            self::TEMPLATE_DATABASE                       => Str::replace('-', '_', $slug),
            self::TEMPLATE_TITLE                          => $title,
        ];

        $changedFiles = [];

        foreach (self::RENAMED_FILES as $file) {
            if ($this->rewrite($basePath, $file, fn (string $contents): string => strtr($contents, $renames))) {
                $changedFiles[] = $file;
            }
        }

        $resetVersion = fn (string $contents): string => (string) preg_replace(
            '/^(\s*"(?:version|\.)":\s*)"[^"]+"/m',
            '$1"' . self::INITIAL_VERSION . '"',
            $contents,
            limit: 1,
        );

        foreach (['package.json', '.release-please-manifest.json'] as $file) {
            if ($this->rewrite($basePath, $file, $resetVersion)) {
                $changedFiles[] = $file;
            }
        }

        $setDescription = fn (string $contents): string => (string) preg_replace_callback(
            '/^(\s*"description":\s*)"(?:[^"\\\\]|\\\\.)*"/m',
            fn (array $matches): string => $matches[1] . json_encode($description, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            $contents,
            limit: 1,
        );

        if ($description !== null && $this->rewrite($basePath, 'composer.json', $setDescription)) {
            $changedFiles[] = 'composer.json';
        }

        if ($this->rewrite($basePath, 'CHANGELOG.md', fn (): string => "# Changelog\n")) {
            $changedFiles[] = 'CHANGELOG.md';
        }

        $changedFiles = array_values(array_unique($changedFiles));
        sort($changedFiles);

        return $changedFiles;
    }

    /**
     * @param  callable(string): string  $transform
     */
    private function rewrite(string $basePath, string $file, callable $transform): bool
    {
        $path = "{$basePath}/{$file}";

        if (! File::isFile($path)) {
            return false;
        }

        $contents  = File::get($path);
        $rewritten = $transform($contents);

        if ($rewritten === $contents) {
            return false;
        }

        File::put($path, $rewritten);

        return true;
    }
}
