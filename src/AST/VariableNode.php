<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\AST;

use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\Exception\UndefinedVariableException;
use nicoSWD\Rule\TokenStream\Token\TokenArray;

final class VariableNode extends Node
{
    public function __construct(
        public readonly string $name,
        public readonly int $offset = 0,
    ) {
    }

    /**
     * @throws ParserException
     */
    public function evaluate(EvaluationContext $context): mixed
    {
        try {
            $token = $context->variableRegistry->get($this->name);
        } catch (UndefinedVariableException) {
            throw ParserException::undefinedVariable($this->name, $this->offset);
        }

        if ($token instanceof TokenArray) {
            return $token->toArray();
        }

        return $token->getValue();
    }
}
