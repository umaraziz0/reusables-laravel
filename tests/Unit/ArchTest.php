<?php

declare(strict_types=1);

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Logging\RedactSensitiveData;
use App\Actions\Logging\WriteLog;
use App\Jobs\LogActivity;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

arch()->preset()->php();
// arch()->preset()->laravel(); // prevents Controllers to have methods other than default
// arch()->preset()->strict(); // cannot have protected methods
arch()->preset()->security();

arch()
    ->expect('App')
    ->toUseStrictTypes()
    ->not->toUse(['die', 'dd', 'dump']);

arch()->expect(['env'])->not->toBeUsed(); // prevents direct env() calls except from configs
arch()->expect(['config'])->not->toBeUsed();

arch()
    ->expect('App')
    ->not->toUse([Log::class, LogManager::class, LoggerInterface::class, 'logger', 'info'])
    ->ignoring(WriteLog::class); // operational logs go through WriteLog

arch()->expect(LogActivity::class)->toImplement(ShouldBeEncrypted::class);

arch()
    ->expect('App\Actions')
    ->not->toHavePublicMethodsBesides(['__construct', 'handle'])
    ->ignoring([CreateNewUser::class, ResetUserPassword::class, RedactSensitiveData::class]); // Fortify contracts dictate method names; RedactSensitiveData is a static helper bundle

it('forbids Services and Repositories layers', function (): void {
    expect(app_path('Services'))->not->toBeDirectory()
        ->and(app_path('Repositories'))->not->toBeDirectory();
});

it('forbids config() string-key reads in migrations and tests', function (): void {
    $pattern = '/' . '\bconfig\s*\(\s*[\'"]' . '/';

    $paths = [
        ...File::glob(base_path('database/migrations/*.php')),
        ...File::allFiles(base_path('tests')),
    ];

    foreach ($paths as $path) {
        $file = is_string($path) ? $path : $path->getPathname();

        if (! str_ends_with($file, '.php')) {
            continue;
        }

        if (str_ends_with($file, 'ArchTest.php')) {
            continue;
        }

        $contents = File::get($file);

        expect($contents)->not->toMatch($pattern);
    }
});

it('keeps CLAUDE.md as an import of the Boost-managed AGENTS.md', function (): void {
    expect(File::get(base_path('CLAUDE.md')))->toBe("@AGENTS.md\n");
});

it('keeps excluded and merged guidance out of AGENTS.md', function (): void {
    expect(File::get(base_path('AGENTS.md')))
        ->not->toContain('<!-- sloppy:begin -->')
        ->not->toContain('=== deployments rules ===')
        ->not->toContain('=== pestphp/pest-plugin-agent/')
        ->not->toContain('=== mrpunyapal/laravel-auditor/');
});
