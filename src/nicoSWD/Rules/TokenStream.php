<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

use Closure;
use Iterator;
use nicoSWD\Rules\Exceptions\UndefinedVariableException;
use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\Tokens\TokenEncapsedString;
use nicoSWD\Rules\Tokens\TokenFunction;
use nicoSWD\Rules\Tokens\TokenOpeningArray;
use nicoSWD\Rules\Tokens\TokenRegex;
use nicoSWD\Rules\Tokens\TokenString;
use nicoSWD\Rules\Tokens\TokenVariable;
use nicoSWD\Rules\AST\Nodes\NodeArray;
use nicoSWD\Rules\AST\Nodes\NodeFunction;
use nicoSWD\Rules\AST\Nodes\NodeString;
use nicoSWD\Rules\AST\Nodes\NodeVariable;

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
            case TokenString::class:
            case TokenEncapsedString::class:
            case TokenRegex::class:
                $current = new NodeString($this);
                break;
            case TokenOpeningArray::class:
                $current = new NodeArray($this);
                break;
            case TokenVariable::class:
                $current = new NodeVariable($this);
                break;
            case TokenFunction::class:
                $current = new NodeFunction($this);
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

    public function getVariable(string $name): BaseToken
    {
        try {
            return $this->ast->getVariable($name);
        } catch (UndefinedVariableException $e) {
            throw new Exceptions\ParserException(sprintf(
                'Undefined variable "%s" at position %d on line %d',
                $name,
                $this->stack->current()->getPosition(),
                $this->stack->current()->getLine()
            ));
        }
    }

    public function getFunction(string $functionName): Closure
    {
        return $this->ast->getFunction($functionName);
    }

    public function getStack(): Stack
    {
        return $this->stack;
    }
}
