<?php
/**
 * Created by PhpStorm.
 * User: Nico
 * Date: 17/07/15
 * Time: 14:10
 */

namespace nicoSWD\Rules\Tokens;

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
     * @var \SplObjectStorage
     */
    protected $stack;

    /**
     * @param string            $value
     * @param int               $offset
     * @param \SplObjectStorage $stack
     */
    public function __construct($value, $offset, \SplObjectStorage $stack)
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
     * @return string
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
     * @return string
     */
    final public function getOriginalValue()
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        $offset = 0;

        foreach ($this->stack as $token) {
            if ($token === $this) {
                break;
            } elseif ($token instanceof TokenNewline) {
                $offset = 0;
                continue;
            }

            $offset += strlen($token->getOriginalValue());
        }

        return $offset;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        $line = 1;

        foreach ($this->stack as $token) {
            if ($token instanceof TokenNewline) {
                $line++;
            } elseif ($token === $this) {
                break;
            }
        }
        return $line;
    }
}
