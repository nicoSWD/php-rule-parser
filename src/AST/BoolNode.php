<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\AST;

final class BoolNode extends ValueNode
{
    public function __construct(
        public readonly bool $value,
    ) {
    }

    public function evaluate(EvaluationContext $context): bool
    {
        return $this->value;
    }
}
