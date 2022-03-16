<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream;

use Closure;
use Iterator;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\Grammar\CallableUserFunctionInterface;
use nicoSWD\Rule\TokenStream\Token\BaseToken;

class TokenIterator implements Iterator
{
    public function __construct(
        private readonly Iterator $stack,
        private readonly TokenStream $tokenStream,
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

    /** @throws ParserException */
    public function current(): BaseToken
    {
        return $this->getCurrentToken()->createNode($this);
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
            return $this->tokenStream->getVariable($variableName);
        } catch (Exception\UndefinedVariableException) {
            throw ParserException::undefinedVariable($variableName, $this->getCurrentToken());
        }
    }

    /** @throws ParserException */
    public function getFunction(string $functionName): Closure
    {
        try {
            return $this->tokenStream->getFunction($functionName);
        } catch (Exception\UndefinedFunctionException) {
            throw ParserException::undefinedFunction($functionName, $this->getCurrentToken());
        }
    }

    /** @throws ParserException */
    public function getMethod(string $methodName, BaseToken $token): CallableUserFunctionInterface
    {
        try {
            return $this->tokenStream->getMethod($methodName, $token);
        } catch (Exception\UndefinedMethodException) {
            throw ParserException::undefinedMethod($methodName, $this->getCurrentToken());
        } catch (Exception\ForbiddenMethodException) {
            throw ParserException::forbiddenMethod($methodName, $this->getCurrentToken());
        }
    }
}
