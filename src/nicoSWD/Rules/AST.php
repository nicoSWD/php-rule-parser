<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

use Iterator;

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
                $current = new AST\NodeString($this);
                break;
            case $current instanceof Tokens\TokenOpeningArray:
                $current = new AST\NodeArray($this);
                break;
            case $current instanceof Tokens\TokenFunction:
                $current = new AST\NodeFunction($this);
                break;
            case $current instanceof Tokens\TokenVariable:
                $current = new AST\NodeVariable($this);
                break;
        }

        while ($current->supportsMethodCalls() && $this->hasMethodCall()) {
            $method = $this->getMethod($current);
            $current = $method->call($this->getFunctionArgs());
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
     * @return mixed
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

        return $this->variables[$name];
    }

    /**
     * @return Stack
     */
    public function getStack()
    {
        return $this->stack;
    }
}
