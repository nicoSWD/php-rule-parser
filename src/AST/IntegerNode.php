<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\AST;

final class IntegerNode extends ValueNode
{
    public function __construct(
        public readonly int $value,
    ) {
    }

    public function evaluate(EvaluationContext $context): int
    {
        return $this->value;
    }
}
