<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\AST;

use nicoSWD\Rule\Parser\Exception\ParserException;

final class ComparisonNode extends Node
{
    public function __construct(
        public readonly Node $left,
        public readonly Node $right,
        public readonly ComparisonOperator $operator,
    ) {
    }

    /**
     * @throws ParserException
     */
    public function evaluate(EvaluationContext $context): bool
    {
        $leftValue = $this->left->evaluate($context);
        $rightValue = $this->right->evaluate($context);

        return match ($this->operator) {
            ComparisonOperator::EQUAL => $leftValue == $rightValue,
            ComparisonOperator::EQUAL_STRICT => $leftValue === $rightValue,
            ComparisonOperator::NOT_EQUAL => $leftValue != $rightValue,
            ComparisonOperator::NOT_EQUAL_STRICT => $leftValue !== $rightValue,
            ComparisonOperator::LESS_THAN => $leftValue < $rightValue,
            ComparisonOperator::GREATER_THAN => $leftValue > $rightValue,
            ComparisonOperator::LESS_THAN_EQUAL => $leftValue <= $rightValue,
            ComparisonOperator::GREATER_THAN_EQUAL => $leftValue >= $rightValue,
            ComparisonOperator::IN => $this->evaluateIn($leftValue, $rightValue),
            ComparisonOperator::NOT_IN => !$this->evaluateIn($leftValue, $rightValue),
        };
    }

    /**
     * @throws ParserException
     */
    private function evaluateIn(mixed $leftValue, mixed $rightValue): bool
    {
        if (!is_array($rightValue)) {
            throw ParserException::expectedArray(gettype($rightValue));
        }

        return in_array($leftValue, $rightValue, strict: true);
    }
}
