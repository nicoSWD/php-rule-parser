<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream;

use ArrayIterator;
use Closure;
use Iterator;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\Grammar\CallableUserFunction;
use nicoSWD\Rule\TokenStream\Token\BaseToken;

class TokenStream implements Iterator
{
    /** @var ArrayIterator */
    private $stack;
    /** @var AST */
    private $ast;

    public function create(ArrayIterator $stack, AST $ast)
    {
        $stream = new self();
        $stream->stack = $stack;
        $stream->ast = $ast;

        return $stream;
    }

    public function next()
    {
        $this->stack->next();
    }

    public function valid(): bool
    {
        return $this->stack->valid();
    }

    public function current(): BaseToken
    {
        return $this->getCurrentToken()->createNode($this);
    }

    public function key(): int
    {
        return $this->stack->key();
    }

    public function rewind()
    {
        $this->stack->rewind();
    }

    public function getStack(): ArrayIterator
    {
        return $this->stack;
    }

    private function getCurrentToken(): BaseToken
    {
        return $this->stack->current();
    }

    public function getVariable(string $variableName): BaseToken
    {
        try {
            return $this->ast->getVariable($variableName);
        } catch (Exception\UndefinedVariableException $e) {
            throw ParserException::undefinedVariable($variableName, $this->getCurrentToken());
        }
    }

    public function getFunction(string $functionName): Closure
    {
        try {
            return $this->ast->getFunction($functionName);
        } catch (Exception\UndefinedFunctionException $e) {
            throw ParserException::undefinedFunction($functionName, $this->getCurrentToken());
        }
    }

    public function getMethod(string $methodName, BaseToken $token): CallableUserFunction
    {
        try {
            return $this->ast->getMethod($methodName, $token);
        } catch (Exception\UndefinedMethodException $e) {
            throw ParserException::undefinedMethod($methodName, $this->getCurrentToken());
        }
    }
}
