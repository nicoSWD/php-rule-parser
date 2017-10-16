<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Expressions;

use nicoSWD\Rules\Exceptions\ExpressionFactoryException;
use nicoSWD\Rules\Tokens;

final class Factory
{
    /** @var string[] */
    private $classLookup = [
        Tokens\TokenEqual::class          => EqualExpression::class,
        Tokens\TokenEqualStrict::class    => EqualStrictExpression::class,
        Tokens\TokenNotEqual::class       => NotEqualExpression::class,
        Tokens\TokenNotEqualStrict::class => NotEqualStrictExpression::class,
        Tokens\TokenGreater::class        => GreaterThanExpression::class,
        Tokens\TokenSmaller::class        => LessThanExpression::class,
        Tokens\TokenSmallerEqual::class   => LessThanEqualExpression::class,
        Tokens\TokenGreaterEqual::class   => GreaterThanEqualExpression::class,
        Tokens\TokenIn::class             => InExpression::class
    ];

    public function createFromOperator($operator): BaseExpression
    {
        $class = get_class($operator);

        if (!isset($this->classLookup[$class])) {
            throw new ExpressionFactoryException(sprintf(
                'Unknown operator "%s"',
                $class
            ));
        }

        return new $this->classLookup[$class]();
    }

    public function mapOperatorToClass(string $operator, $class)
    {
        $this->classLookup[$operator] = $class;
    }
}
