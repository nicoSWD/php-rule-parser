<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\AST;

final class FloatNode extends ValueNode
{
    public function __construct(
        public readonly float $value,
    ) {
    }

    public function evaluate(EvaluationContext $context): float
    {
        return $this->value;
    }
}
