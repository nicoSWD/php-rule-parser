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
     * @var int
     */
    protected $line = 1;

    /**
     * @param string $value
     * @param int    $offset
     * @param int    $line
     */
    public function __construct($value, $offset, $line)
    {
        $this->value = $value;
        $this->offset = $offset;
        $this->line = $line;
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
     * Some tokens can be represented by different symbols,
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
    public function getLine()
    {
        return $this->line;
    }
}
