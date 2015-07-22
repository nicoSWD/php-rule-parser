<?php
/**
 * Created by PhpStorm.
 * User: nico
 * Date: 21/07/15
 * Time: 17:35
 */

namespace nicoSWD\Rules\Expressions;

use nicoSWD\Rules\Exceptions\ExpressionFactoryException;

/**
 * Class Factory
 * @package nicoSWD\Rules\Expressions
 */
final class Factory
{
    /**
     * @param string $operator
     * @return BaseExpression
     * @throws ExpressionFactoryException
     */
    public static function createFromOperator($operator)
    {
        switch ($operator) {
            case '=':
                return new EqualExpression();
            case '!=':
                return new NotEqualExpression();
            case '>':
                return new GreaterThanExpression();
            case '<':
                return new LessThanExpression();
            case '<=':
                return new LessThanEqualExpression();
            case '>=':
                return new GreaterThanEqualExpression();
        }

        throw new ExpressionFactoryException(sprintf(
            'Unknown operator "%s"',
            $operator
        ));
    }
}
