<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Tokens;

use nicoSWD\Rules\Stack;

/**
 * Class BaseToken
 * @package nicoSWD\Rules\Tokens
 */
abstract class BaseToken
{
    /**
     * @var string
     */
    protected $value = '';

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
    protected $position = \null;

    /**
     * @var int
     */
    protected $line = \null;

    /**
     * @param mixed $value
     * @param int   $offset
     * @param Stack $stack
     */
    public function __construct($value, $offset = 0, Stack $stack = \null)
    {
        $this->value = $value;
        $this->offset = $offset;
        $this->stack = $stack;
    }

    /**
     * @return int
     */
    abstract public function getGroup();

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

    /**
     * Returns offset in the whole rule string.
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset = 0)
    {
        $this->offset = $offset;
    }

    /**
     * @return Stack
     */
    public function getStack()
    {
        return $this->stack;
    }

    /**
     * @param Stack $stack
     */
    public function setStack(Stack $stack)
    {
        $this->stack = $stack;
    }

    /**
     * @return bool
     */
    public function supportsMethodCalls()
    {
        return \false;
    }

    /**
     * Returns position in the line the token is placed in.
     *
     * @return int
     */
    public function getPosition()
    {
        if (!isset($this->position)) {
            $this->getLineAndPosition();
        }

        return $this->position;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        if (!isset($this->line)) {
            $this->getLineAndPosition();
        }

        return $this->line;
    }

    /**
     * @since 0.3.5
     * @internal
     * @return void
     */
    private function getLineAndPosition()
    {
        $this->line = 1;

        foreach ($this->stack as $token) {
            $sumPosition = \true;

            if ($token === $this) {
                break;
            } elseif ($token instanceof TokenNewline) {
                $this->line += 1;
                $this->position = 0;
                $sumPosition = \false;
            } elseif ($token instanceof TokenComment) {
                $this->line += substr_count($token->getValue(), "\n");
            }

            if ($sumPosition) {
                $this->position += strlen($token->getOriginalValue());
            }
        }
    }
}
