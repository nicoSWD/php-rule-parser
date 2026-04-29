<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\AST;

final class AdditionNode extends Node
{
    public function __construct(
        public readonly Node $left,
        public readonly Node $right,
    ) {
    }

    public function evaluate(EvaluationContext $context): mixed
    {
        $left = $this->left->evaluate($context);
        $right = $this->right->evaluate($context);

        if (is_string($left) || is_string($right)) {
            return (string) $left . (string) $right;
        }

        return $left + $right;
    }
}
