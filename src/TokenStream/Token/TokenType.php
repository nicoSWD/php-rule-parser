<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Token;

enum TokenType
{
    case OPERATOR;
    case VALUE;
    case LOGICAL;
    case VARIABLE;
    case COMMENT;
    case SPACE;
    case UNKNOWN;
    case PARENTHESIS;
    case SQUARE_BRACKET;
    case COMMA;
    case METHOD;
    case FUNCTION;

    public static function isValue(BaseToken $token): bool
    {
        return $token->getType() === self::VALUE;
    }
}
