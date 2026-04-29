<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\AST;

use nicoSWD\Rule\Parser\Exception\ParserException;

final class LogicalNode extends Node
{
    public function __construct(
        public readonly Node $left,
        public readonly Node $right,
        public readonly LogicalOperator $operator,
    ) {
    }

    /**
     * @throws ParserException
     */
    public function evaluate(EvaluationContext $context): bool
    {
        $left = (bool) $this->left->evaluate($context);

        // Short-circuit evaluation
        return match ($this->operator) {
            LogicalOperator::AND => $left && (bool) $this->right->evaluate($context),
            LogicalOperator::OR => $left || (bool) $this->right->evaluate($context),
        };
    }
}
