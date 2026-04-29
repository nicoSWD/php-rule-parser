<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\AST;

use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\Exception\ForbiddenMethodException;
use nicoSWD\Rule\TokenStream\Exception\UndefinedMethodException;
use nicoSWD\Rule\TokenStream\Token\TokenKind;

final class MethodCallNode extends Node
{
    /** @param Node[] $arguments */
    public function __construct(
        public readonly Node $object,
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
        $objectValue = $this->object->evaluate($context);
        $objectToken = $context->tokenFactory->createFromPHPType($objectValue);

        // For regex nodes, use the original token to preserve type information
        if ($this->object instanceof RegexNode) {
            $objectToken = $this->object->originalToken;
        }

        $args = $this->resolveArguments($context);

        try {
            $method = $context->methodRegistry->get($this->name, $objectToken);
        } catch (UndefinedMethodException) {
            throw ParserException::undefinedMethod($this->name, $this->offset);
        } catch (ForbiddenMethodException) {
            throw ParserException::forbiddenMethod($this->name, $objectToken);
        }

        $result = $method->call(...$args);

        if ($result->isOfKind(TokenKind::ARRAY)) {
            return $result->toArray();
        }

        return $result->getValue();
    }

    /**
     * @throws ParserException
     */
    private function resolveArguments(EvaluationContext $context): array
    {
        $resolved = [];

        foreach ($this->arguments as $argument) {
            // Preserve the original token for regex arguments
            if ($argument instanceof RegexNode) {
                $resolved[] = $argument->originalToken;
                continue;
            }

            $value = $argument->evaluate($context);
            $resolved[] = $context->tokenFactory->createFromPHPType($value);
        }

        return $resolved;
    }
}
