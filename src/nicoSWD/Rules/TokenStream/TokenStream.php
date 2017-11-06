<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\TokenStream;

use Closure;
use Iterator;
use nicoSWD\Rules\Parser\Exception\ParserException;
use nicoSWD\Rules\Grammar\CallableUserFunction;
use nicoSWD\Rules\Tokenizer\TokenStack;
use nicoSWD\Rules\TokenStream\Token;

class TokenStream implements Iterator
{
    /** @var TokenStack */
    protected $stack;
    /** @var AST */
    private $ast;

    public function create(TokenStack $stack, AST $ast)
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

    public function current()
    {
        if ($this->stack->valid()) {
            return $this->stack->current()->createNode($this);
        }

        return null;
    }

    public function key(): int
    {
        return $this->stack->key();
    }

    public function rewind()
    {
        $this->stack->rewind();
    }

    public function getVariable(string $name): Token\BaseToken
    {
        try {
            return $this->ast->getVariable($name);
        } catch (Exception\UndefinedVariableException $e) {
            throw ParserException::undefinedVariable($name, $this->stack->current());
        }
    }

    public function getFunction(string $functionName): Closure
    {
        return $this->ast->getFunction($functionName);
    }

    public function getMethod(string $methodName, Token\BaseToken $token): CallableUserFunction
    {
        try {
            return $this->ast->getMethod($methodName, $token);
        } catch (Exception\UndefinedMethodException $e) {
            throw ParserException::undefinedMethod($methodName, $this->stack->current());
        }
    }

    public function getStack(): TokenStack
    {
        return $this->stack;
    }
}
