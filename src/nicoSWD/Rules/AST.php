<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules;

use Iterator;
use nicoSWD\Rules\Tokens\{
    BaseToken,
    TokenFactory,
    TokenFunction,
    TokenOpeningArray,
    TokenRegex,
    TokenString,
    TokenVariable
};
use nicoSWD\Rules\AST\Nodes\{
    NodeArray,
    NodeFunction,
    NodeString,
    NodeVariable
};

final class AST implements Iterator
{
    public $parser;

    /**
     * @var Stack
     */
    protected $stack;

    /**
     * @var mixed[]
     */
    protected $variables = [];

    /**
     * @param Stack   $stack
     * @param mixed[] $variables
     */
    public function __construct(Stack $stack, Parser $parser)
    {
        $this->stack = $stack;
        $this->variables = $parser->variables;
        $this->parser = $parser;
    }

    public function next()
    {
        $this->stack->next();
    }

    public function valid() : bool
    {
        return $this->stack->valid();
    }

    public function current()
    {
        $current = $this->stack->current();

        switch (true) {
            default:
                return $current;
            case $current instanceof TokenString:
            case $current instanceof TokenRegex:
                $current = new NodeString($this);
                break;
            case $current instanceof TokenOpeningArray:
                $current = new NodeArray($this);
                break;
            case $current instanceof TokenVariable:
                $current = new NodeVariable($this);
                break;
            case $current instanceof TokenFunction:
                $current = new NodeFunction($this);
                break;
        }

        return $current->getNode();
    }

    /**
     * @codeCoverageIgnore
     */
    public function key() : int
    {
        return $this->stack->key();
    }

    public function rewind()
    {
        $this->stack->rewind();
    }

    /**
     * @throws Exceptions\ParserException
     */
    public function getVariable(string $name) : BaseToken
    {
        if (!array_key_exists($name, $this->variables)) {
            $token = $this->stack->current();

            throw new Exceptions\ParserException(sprintf(
                'Undefined variable "%s" at position %d on line %d',
                $name,
                $token->getPosition(),
                $token->getLine()
            ));
        }

        return TokenFactory::createFromPHPType($this->variables[$name]);
    }

    public function getStack() : Stack
    {
        return $this->stack;
    }
}
