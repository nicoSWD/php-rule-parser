<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Expression;

use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\Token;
use nicoSWD\Rule\TokenStream\Token\BaseToken;

class ExpressionFactory implements ExpressionFactoryInterface
{
    /** @throws ParserException */
    public function createFromOperator(BaseToken $operator): BaseExpression
    {
        return match (get_class($operator)) {
            Token\TokenEqual::class => new EqualExpression(),
            Token\TokenEqualStrict::class => new EqualStrictExpression(),
            Token\TokenNotEqual::class => new NotEqualExpression(),
            Token\TokenNotEqualStrict::class => new NotEqualStrictExpression(),
            Token\TokenGreater::class => new GreaterThanExpression(),
            Token\TokenSmaller::class => new LessThanExpression(),
            Token\TokenSmallerEqual::class => new LessThanEqualExpression(),
            Token\TokenGreaterEqual::class => new GreaterThanEqualExpression(),
            Token\TokenIn::class => new InExpression(),
            Token\TokenNotIn::class => new NotInExpression(),
            default => throw ParserException::unknownOperator($operator),
        };
    }
}
