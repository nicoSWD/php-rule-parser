<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\AST;

final class ModuloNode extends Node
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
            return NAN;
        }

        return $left % $right;
    }
}
