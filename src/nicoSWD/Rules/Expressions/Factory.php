<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3
 * @author      Nicolas Oelgart <nico@oelgart.com>
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
     * @var array
     */
    private $classLookup = [
        '=='  => '\nicoSWD\Rules\Expressions\EqualExpression',
        '===' => '\nicoSWD\Rules\Expressions\EqualStrictExpression',
        '!='  => '\nicoSWD\Rules\Expressions\NotEqualExpression',
        '!==' => '\nicoSWD\Rules\Expressions\NotEqualStrictExpression',
        '>'   => '\nicoSWD\Rules\Expressions\GreaterThanExpression',
        '<'   => '\nicoSWD\Rules\Expressions\LessThanExpression',
        '<='  => '\nicoSWD\Rules\Expressions\LessThanEqualExpression',
        '>='  => '\nicoSWD\Rules\Expressions\GreaterThanEqualExpression',
        'in'  => '\nicoSWD\Rules\Expressions\InExpression'
    ];

    /**
     * @param string $operator
     * @return BaseExpression
     * @throws ExpressionFactoryException
     */
    public function createFromOperator($operator)
    {
        if (isset($this->classLookup[$operator])) {
            return new $this->classLookup[$operator]();
        }

        throw new ExpressionFactoryException(sprintf(
            'Unknown operator "%s"',
            $operator
        ));
    }

    /**
     * @param string $operator
     * @param string $class
     * @since 0.3.2
     */
    public function mapOperatorToClass($operator, $class)
    {
        $this->classLookup[$operator] = $class;
    }
}
