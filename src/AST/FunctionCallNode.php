<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\AST;

use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\Exception\UndefinedFunctionException;

final class FunctionCallNode extends Node
{
    /** @param Node[] $arguments */
    public function __construct(
        public readonly string $name,
        public readonly array $arguments,
        public readonly int $offset = 0,
    ) {
    }

    /**
     * @throws ParserException
     */
    public function evaluate(EvaluationContext $context): mixed
    {
        $args = $this->resolveArguments($context);

        try {
            $function = $context->functionRegistry->get($this->name);
        } catch (UndefinedFunctionException) {
            throw ParserException::undefinedFunction($this->name, $this->offset);
        }

        return $function->call(...$args)->getValue();
    }

    /**
     * @throws ParserException
     */
    private function resolveArguments(EvaluationContext $context): array
    {
        $resolved = [];

        foreach ($this->arguments as $argument) {
            $value = $argument->evaluate($context);
            $resolved[] = $value;
        }

        return $resolved;
    }
}
