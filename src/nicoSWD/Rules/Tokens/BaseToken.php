<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules\Tokens;

use nicoSWD\Rules\Stack;

abstract class BaseToken
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var Stack
     */
    protected $stack;

    /**
     * @var int
     */
    protected $position = null;

    /**
     * @var int
     */
    protected $line = null;

    /**
     * @param mixed $value
     * @param int   $offset
     * @param Stack $stack
     */
    public function __construct($value, int $offset = 0, Stack $stack = null)
    {
        $this->value = $value;
        $this->offset = $offset;
        $this->stack = $stack;
    }

    abstract public function getGroup() : int;

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Some tokens can be represented by different operators,
     * so the original value is used for error reporting,
     * while the other one is used internally.
     *
     * @return mixed
     */
    final public function getOriginalValue()
    {
        return $this->value;
    }

    public function getOffset() : int
    {
        return $this->offset;
    }

    public function setOffset(int $offset = 0)
    {
        $this->offset = $offset;
    }

    public function getStack() : Stack
    {
        return $this->stack;
    }

    public function setStack(Stack $stack)
    {
        $this->stack = $stack;
    }

    public function supportsMethodCalls() : bool
    {
        return false;
    }

    public function getPosition() : int
    {
        if (!isset($this->position)) {
            $this->getLineAndPosition();
        }

        return $this->position;
    }

    public function getLine() : int
    {
        if (!isset($this->line)) {
            $this->getLineAndPosition();
        }

        return $this->line;
    }

    private function getLineAndPosition()
    {
        $this->line = 1;
        $this->position = 0;

        foreach ($this->stack as $token) {
            $sumPosition = true;

            if ($token === $this) {
                break;
            } elseif ($token instanceof TokenNewline) {
                $this->line += 1;
                $this->position = 0;
                $sumPosition = false;
            } elseif ($token instanceof TokenComment) {
                $this->line += substr_count($token->getValue(), "\n");
            }

            if ($sumPosition) {
                $this->position += strlen($token->getOriginalValue());
            }
        }
    }
}
