<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

test('composer.json has no version for releases to bump', function (): void {
    // composer.lock's content hash covers composer.json's version, so a release
    // bump without a lock update leaves every fresh clone with an outdated lock.
    $releasePleasePackage = File::json(base_path('release-please-config.json'))['packages']['.'];

    expect(File::json(base_path('composer.json')))->not->toHaveKey('version')
        ->and($releasePleasePackage['extra-files'] ?? [])->not->toContain('composer.json');
});
