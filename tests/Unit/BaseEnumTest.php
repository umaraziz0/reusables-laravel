<?php

declare(strict_types=1);

use App\Enums\Role;

test('values returns backed enum values', function (): void {
    expect(Role::values())->toBe([
        'superadmin',
        'admin',
    ]);
});
