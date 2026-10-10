<?php

declare(strict_types=1);

namespace Tests\PHPStan;

use Override;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Tests\PHPStan\Rules\ArrayItemLayoutRule;

/**
 * Supplies the rule under test so ArrayItemLayoutRuleTest can be a Pest test,
 * which Pest's TIA requires; a Pest test file can't declare getRule() itself.
 *
 * @extends RuleTestCase<ArrayItemLayoutRule>
 */
class ArrayItemLayoutRuleTestCase extends RuleTestCase
{
    #[Override]
    protected function getRule(): Rule
    {
        return new ArrayItemLayoutRule;
    }
}
