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
