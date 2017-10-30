<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 *
 * @link        https://github.com/nicoSWD
 *
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */

namespace nicoSWD\Rules\Expressions;

use nicoSWD\Rules\Tokens;

final class ExpressionFactory implements ExpressionFactoryInterface
{
    private $classLookup = [
        Tokens\TokenEqual::class          => EqualExpression::class,
        Tokens\TokenEqualStrict::class    => EqualStrictExpression::class,
        Tokens\TokenNotEqual::class       => NotEqualExpression::class,
        Tokens\TokenNotEqualStrict::class => NotEqualStrictExpression::class,
        Tokens\TokenGreater::class        => GreaterThanExpression::class,
        Tokens\TokenSmaller::class        => LessThanExpression::class,
        Tokens\TokenSmallerEqual::class   => LessThanEqualExpression::class,
        Tokens\TokenGreaterEqual::class   => GreaterThanEqualExpression::class,
        Tokens\TokenIn::class             => InExpression::class,
    ];

    public function createFromOperator(Tokens\BaseToken $operator): BaseExpression
    {
        return new $this->classLookup[get_class($operator)]();
    }
}
