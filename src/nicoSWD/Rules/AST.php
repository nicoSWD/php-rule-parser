<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

use Iterator;
use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\Tokens\TokenFactory;

/**
 * Class AST
 * @package nicoSWD\Rules
 */
final class AST implements Iterator
{
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
    public function __construct(Stack $stack, array $variables = [])
    {
        $this->stack = $stack;
        $this->variables = $variables;
    }

    /**
     * @return void
     */
    public function next()
    {
        $this->stack->next();
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->stack->valid();
    }

    /**
     * @return Tokens\BaseToken
     */
    public function current()
    {
        $current = $this->stack->current();

        switch (\true) {
            default:
                return $current;
            case $current instanceof Tokens\TokenString:
            case $current instanceof Tokens\TokenRegex:
                $current = new AST\Nodes\NodeString($this);
                break;
            case $current instanceof Tokens\TokenOpeningArray:
                $current = new AST\Nodes\NodeArray($this);
                break;
            case $current instanceof Tokens\TokenVariable:
                $current = new AST\Nodes\NodeVariable($this);
                break;
            case $current instanceof Tokens\TokenFunction:
                $current = new AST\Nodes\NodeFunction($this);
                break;
        }

        return $current->getNode();
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return $this->stack->key();
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->stack->rewind();
    }

    /**
     * @param string $name
     * @return BaseToken
     * @throws Exceptions\ParserException
     */
    public function getVariable($name)
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

    /**
     * @return Stack
     */
    public function getStack()
    {
        return $this->stack;
    }
}
