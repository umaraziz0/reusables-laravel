<?php

declare(strict_types=1);

namespace Tests\Unit\PHPStan;

use Override;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Tests\PHPStan\Rules\ArrayItemLayoutRule;

/**
 * @extends RuleTestCase<ArrayItemLayoutRule>
 */
final class ArrayItemLayoutRuleTest extends RuleTestCase
{
    #[Override]
    protected function getRule(): Rule
    {
        return new ArrayItemLayoutRule;
    }

    public function test_it_flags_multiline_arrays_that_share_item_or_bracket_lines(): void
    {
        $this->analyse([__DIR__ . '/../../PHPStan/Fixtures/array-item-layout.php'], [
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
    }
}
