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
use nicoSWD\Rules\Exceptions\ParserException;
use nicoSWD\Rules\Grammar\CallableUserFunction;
use nicoSWD\Rules\Tokenizer\Stack;
use nicoSWD\Rules\Tokens;
use nicoSWD\Rules\TokenStream\Nodes;

class TokenStream implements Iterator
{
    /** @var Stack */
    protected $stack;

    /** @var AST */
    private $ast;

    public function create(Stack $stack, AST $ast)
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
        $current = $this->stack->current();

        switch (get_class($current)) {
            default:
                return $current;
            case Tokens\TokenString::class:
            case Tokens\TokenEncapsedString::class:
            case Tokens\TokenRegex::class:
                $current = new Nodes\NodeString($this);
                break;
            case Tokens\TokenOpeningArray::class:
                $current = new Nodes\NodeArray($this);
                break;
            case Tokens\TokenVariable::class:
                $current = new Nodes\NodeVariable($this);
                break;
            case Tokens\TokenFunction::class:
                $current = new Nodes\NodeFunction($this);
                break;
        }

        return $current->getNode();
    }

    public function key(): int
    {
        return $this->stack->key();
    }

    public function rewind()
    {
        $this->stack->rewind();
    }

    public function getVariable(string $name): Tokens\BaseToken
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

    public function getMethod(string $methodName, Tokens\BaseToken $token): CallableUserFunction
    {
        try {
            return $this->ast->getMethod($methodName, $token);
        } catch (Exception\UndefinedMethodException $e) {
            throw ParserException::undefinedMethod($methodName, $this->stack->current());
        }
    }

    public function getStack(): Stack
    {
        return $this->stack;
    }
}
