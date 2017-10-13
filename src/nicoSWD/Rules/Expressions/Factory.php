<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules\Expressions;

use nicoSWD\Rules\Exceptions\ExpressionFactoryException;

final class Factory
{
    /**
     * @var string[]
     */
    private $classLookup = [
        '=='  => EqualExpression::class,
        '===' => EqualStrictExpression::class,
        '!='  => NotEqualExpression::class,
        '!==' => NotEqualStrictExpression::class,
        '>'   => GreaterThanExpression::class,
        '<'   => LessThanExpression::class,
        '<='  => LessThanEqualExpression::class,
        '>='  => GreaterThanEqualExpression::class,
        'in'  => InExpression::class
    ];

    /**
     * @throws ExpressionFactoryException
     */
    public function createFromOperator(string $operator) : BaseExpression
    {
        if (!isset($this->classLookup[$operator])) {
            throw new ExpressionFactoryException(sprintf(
                'Unknown operator "%s"',
                $operator
            ));
        }

        return new $this->classLookup[$operator]();
    }

    public function mapOperatorToClass(string $operator, $class)
    {
        $this->classLookup[$operator] = $class;
    }
}
