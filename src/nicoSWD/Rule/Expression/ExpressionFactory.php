<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Expression;

use nicoSWD\Rule\TokenStream\Token;

final class ExpressionFactory implements ExpressionFactoryInterface
{
    private $classLookup = [
        Token\TokenEqual::class          => EqualExpression::class,
        Token\TokenEqualStrict::class    => EqualStrictExpression::class,
        Token\TokenNotEqual::class       => NotEqualExpression::class,
        Token\TokenNotEqualStrict::class => NotEqualStrictExpression::class,
        Token\TokenGreater::class        => GreaterThanExpression::class,
        Token\TokenSmaller::class        => LessThanExpression::class,
        Token\TokenSmallerEqual::class   => LessThanEqualExpression::class,
        Token\TokenGreaterEqual::class   => GreaterThanEqualExpression::class,
        Token\TokenIn::class             => InExpression::class
    ];

    public function createFromOperator(Token\BaseToken $operator): BaseExpression
    {
        return new $this->classLookup[get_class($operator)]();
    }
}
