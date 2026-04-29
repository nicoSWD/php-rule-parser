<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\AST;

abstract class Node
{
    /**
     * Evaluate this node and return its computed value.
     *
     * @throws \RuntimeException
     */
    public function evaluate(EvaluationContext $context): mixed
    {
        throw new \RuntimeException('Node type ' . static::class . ' does not support evaluation');
    }
}
