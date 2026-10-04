<?php

declare(strict_types=1);

use App\Actions\Fields\NormalizeName;

test('normalizes surrounding and repeated whitespace in names', function (string $value, string $expected): void {
    expect(new NormalizeName()->handle($value))->toBe($expected);
})->with([
    'ascii name'   => ['  Jane   Doe  ', 'Jane Doe'],
    'unicode name' => ["\tJosé\n  O'Connor-Smith  ", "José O'Connor-Smith"],
]);
