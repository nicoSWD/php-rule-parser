<?php
/**
 * Created by PhpStorm.
 * User: nico
 * Date: 21/07/15
 * Time: 17:37
 */

namespace nicoSWD\Rules\Expressions;

/**
 * Class BaseExpression
 * @package nicoSWD\Rules\Expressions
 */
abstract class BaseExpression
{
    /**
     * @param string $leftValue
     * @param string $rightValue
     * @return bool
     */
    abstract public function evaluate($leftValue, $rightValue);
}
