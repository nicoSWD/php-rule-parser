<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

use Iterator;
use SplObjectStorage;

/**
 * Class AST
 * @package nicoSWD\Rules
 */
final class AST implements Iterator
{
    /**
     * @var SplObjectStorage
     */
    protected $stack;

    /**
     * @param SplObjectStorage $stack
     */
    public function __construct(SplObjectStorage $stack)
    {
        $this->stack = $stack;
        $this->stack->rewind();
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
                return (new AST\NodeString($current))->getNode();
            case $current instanceof Tokens\TokenOpeningArray:
                return (new AST\NodeArray($current))->getNode();
            case $current instanceof Tokens\TokenFunction:
                return (new AST\NodeFunction($current))->getNode();
        }
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
}
