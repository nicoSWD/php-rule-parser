<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

use Iterator;
use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\Tokens\TokenEncapsedString;
use nicoSWD\Rules\Tokens\TokenFactory;
use nicoSWD\Rules\Tokens\TokenFunction;
use nicoSWD\Rules\Tokens\TokenOpeningArray;
use nicoSWD\Rules\Tokens\TokenRegex;
use nicoSWD\Rules\Tokens\TokenString;
use nicoSWD\Rules\Tokens\TokenVariable;
use nicoSWD\Rules\AST\Nodes\NodeArray;
use nicoSWD\Rules\AST\Nodes\NodeFunction;
use nicoSWD\Rules\AST\Nodes\NodeString;
use nicoSWD\Rules\AST\Nodes\NodeVariable;

final class AST implements Iterator
{
    /** @var Parser */
    public $parser;

    /** @var Stack */
    protected $stack;

    public function __construct(Stack $stack, Parser $parser)
    {
        $this->stack = $stack;
        $this->parser = $parser;
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

    /** @codeCoverageIgnore */
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
        if (!array_key_exists($name, $this->parser->variables)) {
            $token = $this->stack->current();

            throw new Exceptions\ParserException(sprintf(
                'Undefined variable "%s" at position %d on line %d',
                $name,
                $token->getPosition(),
                $token->getLine()
            ));
        }

        return TokenFactory::createFromPHPType($this->parser->variables[$name]);
    }

    public function getStack(): Stack
    {
        return $this->stack;
    }
}
