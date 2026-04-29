<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\TokenStream;

use Iterator;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\Grammar\CallableInterface;
use nicoSWD\Rule\TokenStream\Token\BaseToken;

readonly class TokenIterator implements Iterator
{
    public function __construct(
        private Iterator $stack,
        private VariableRegistry $variableRegistry,
        private FunctionRegistry $functionRegistry,
        private MethodRegistry $methodRegistry,
    ) {
    }

    public function next(): void
    {
        $this->stack->next();
    }

    public function valid(): bool
    {
        return $this->stack->valid();
    }

    public function current(): BaseToken
    {
        return $this->getCurrentToken();
    }

    /**
     * Returns the raw token without creating a node.
     * Used by the AST parser to inspect tokens without resolving them.
     */
    public function peekRaw(): BaseToken
    {
        return $this->getCurrentToken();
    }

    public function key(): int
    {
        return $this->stack->key();
    }

    public function rewind(): void
    {
        $this->stack->rewind();
    }

    /** @return Iterator<BaseToken> */
    public function getStack(): Iterator
    {
        return $this->stack;
    }

    private function getCurrentToken(): BaseToken
    {
        return $this->stack->current();
    }

    /** @throws ParserException */
    public function getVariable(string $variableName): BaseToken
    {
        try {
            return $this->variableRegistry->get($variableName);
        } catch (Exception\UndefinedVariableException) {
            throw ParserException::undefinedVariable($variableName, $this->getCurrentToken()->getOffset());
        }
    }

    /** @throws ParserException */
    public function getFunction(string $functionName): CallableInterface
    {
        try {
            return $this->functionRegistry->get($functionName);
        } catch (Exception\UndefinedFunctionException) {
            throw ParserException::undefinedFunction($functionName, $this->getCurrentToken()->getOffset());
        }
    }

    /** @throws ParserException */
    public function getMethod(string $methodName, BaseToken $token): CallableInterface
    {
        try {
            return $this->methodRegistry->get($methodName, $token);
        } catch (Exception\UndefinedMethodException) {
            throw ParserException::undefinedMethod($methodName, $this->getCurrentToken()->getOffset());
        } catch (Exception\ForbiddenMethodException) {
            throw ParserException::forbiddenMethod($methodName, $this->getCurrentToken());
        }
    }
}
