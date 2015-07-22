<?php
/**
 * Created by PhpStorm.
 * User: nico
 * Date: 21/07/15
 * Time: 17:42
 */

namespace nicoSWD\Rules\Expressions;

/**
 * Class GreaterThanEqualExpression
 * @package nicoSWD\Rules\Expressions
 */
final class GreaterThanEqualExpression extends BaseExpression
{
    /**
     * @param string $leftValue
     * @param string $rightValue
     * @return bool
     */
    public function evaluate($leftValue, $rightValue)
    {
        return $leftValue >= $rightValue;
    }
}
