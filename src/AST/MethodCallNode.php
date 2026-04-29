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

        // For regex nodes, preserve the original token for type detection
        if ($this->object instanceof RegexNode) {
            $objectToken = $this->object->originalToken;
        } else {
            $objectToken = $context->tokenFactory->createFromPHPType($objectValue);
        }

        $args = $this->resolveArguments($context);

        try {
            $method = $context->methodRegistry->get($this->name, $objectToken, $objectValue);
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
            $value = $argument->evaluate($context);
            $resolved[] = $value;
        }

        return $resolved;
    }
}
