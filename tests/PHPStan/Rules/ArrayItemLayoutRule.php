<?php

declare(strict_types=1);

namespace Tests\PHPStan\Rules;

use PhpParser\Node;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Enforces `.ai/rules/general.md` "Keep array item layout consistent": an array sits on one line,
 * or puts each direct item on its own line with both brackets on their own lines. A wrapper whose
 * single item is a nested array (`[$attribute => [ ... ]]`) may share the nested array's lines.
 *
 * @implements Rule<Array_>
 */
final class ArrayItemLayoutRule implements Rule
{
    public function getNodeType(): string
    {
        return Array_::class;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $items = array_values($node->items);

        if ($node->getStartLine() === $node->getEndLine() || $items === [] || $this->isNestedArrayWrapper($items)) {
            return [];
        }

        $problem = $this->layoutProblem($node, $items);

        if ($problem === null) {
            return [];
        }

        return [
            RuleErrorBuilder::message("Multiline array: {$problem}. Put the whole array on one line, or each item on its own line with the brackets on their own lines.")
                ->identifier('arrayLayout.itemPerLine')
                ->line($node->getStartLine())
                ->build(),
        ];
    }

    /**
     * @param  list<ArrayItem>  $items
     */
    private function isNestedArrayWrapper(array $items): bool
    {
        return count($items) === 1 && $items[0]->value instanceof Array_;
    }

    /**
     * @param  list<ArrayItem>  $items
     */
    private function layoutProblem(Array_ $array, array $items): ?string
    {
        if ($items[0]->getStartLine() === $array->getStartLine()) {
            return 'the first item shares the opening bracket line';
        }

        foreach (array_slice($items, 1, preserve_keys: true) as $index => $item) {
            if ($item->getStartLine() === $items[$index - 1]->getEndLine()) {
                return 'several items share a line';
            }
        }

        if (array_last($items)->getEndLine() === $array->getEndLine()) {
            return 'the last item shares the closing bracket line';
        }

        return null;
    }
}
