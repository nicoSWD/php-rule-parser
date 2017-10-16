<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Expressions;

use nicoSWD\Rules\Exceptions\ExpressionFactoryException;
use nicoSWD\Rules\Token;

final class Factory
{
    /** @var string[] */
    private $classLookup = [
        Token::EQUAL            => EqualExpression::class,
        Token::EQUAL_STRICT     => EqualStrictExpression::class,
        Token::NOT_EQUAL        => NotEqualExpression::class,
        Token::NOT_EQUAL_STRICT => NotEqualStrictExpression::class,
        Token::GREATER          => GreaterThanExpression::class,
        Token::SMALLER          => LessThanExpression::class,
        Token::SMALLER_EQUAL    => LessThanEqualExpression::class,
        Token::GREATER_EQUAL    => GreaterThanEqualExpression::class,
        Token::IN               => InExpression::class
    ];

    public function createFromOperator(string $operator): BaseExpression
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
