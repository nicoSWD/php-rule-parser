<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\AST;

final class DivisionNode extends Node
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

        if ($right == 0) {
            if ($left == 0) {
                return NAN;
            }

            return $left > 0 ? INF : -INF;
        }

        return $left / $right;
    }
}
