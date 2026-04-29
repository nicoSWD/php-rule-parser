<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Expression;

use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenKind;
use nicoSWD\Rule\TokenStream\Token\Type\Operator;

final class ExpressionFactory implements ExpressionFactoryInterface
{
    /** @throws ParserException */
    public function createFromOperator(BaseToken & Operator $operator): BaseExpression
    {
        return match ($operator->getKind()) {
            TokenKind::EQUAL => new EqualExpression(),
            TokenKind::EQUAL_STRICT => new EqualStrictExpression(),
            TokenKind::NOT_EQUAL => new NotEqualExpression(),
            TokenKind::NOT_EQUAL_STRICT => new NotEqualStrictExpression(),
            TokenKind::GREATER => new GreaterThanExpression(),
            TokenKind::LESS_THAN => new LessThanExpression(),
            TokenKind::LESS_THAN_EQUAL => new LessThanEqualExpression(),
            TokenKind::GREATER_EQUAL => new GreaterThanEqualExpression(),
            TokenKind::IN => new InExpression(),
            TokenKind::NOT_IN => new NotInExpression(),
            default => throw ParserException::unknownOperator($operator),
        };
    }
}
