<?php

declare(strict_types=1);

use Tests\PHPStan\ArrayItemLayoutRuleTestCase;

pest()->extend(ArrayItemLayoutRuleTestCase::class);

test('it flags multiline arrays that share item or bracket lines', function (): void {
    $this->analyse([__DIR__ . '/Fixtures/array-item-layout.php'], [
        [
            'Multiline array: several items share a line. Put the whole array on one line, or each item on its own line with the brackets on their own lines.',
            12,
        ],
        [
            'Multiline array: the first item shares the opening bracket line. Put the whole array on one line, or each item on its own line with the brackets on their own lines.',
            17,
        ],
        [
            'Multiline array: the last item shares the closing bracket line. Put the whole array on one line, or each item on its own line with the brackets on their own lines.',
            21,
        ],
    ]);
});
