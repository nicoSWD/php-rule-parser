<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\AST;

final class UnaryMinusNode extends Node
{
    public function __construct(
        public readonly Node $node,
        public readonly int $offset = 0,
    ) {
    }

    /**
     * @throws \RuntimeException
     */
    public function evaluate(EvaluationContext $context): mixed
    {
        $value = $this->node->evaluate($context);

        return -$value;
    }
}
