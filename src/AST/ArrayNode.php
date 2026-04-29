<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\AST;

final class ArrayNode extends Node
{
    /** @param Node[] $items */
    public function __construct(
        public readonly array $items,
    ) {
    }

    /**
     * @throws \RuntimeException
     */
    public function evaluate(EvaluationContext $context): array
    {
        $items = [];

        foreach ($this->items as $item) {
            $items[] = $item->evaluate($context);
        }

        return $items;
    }
}
