<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\TokenStream\Token;

use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\TokenCollection;

class TokenFactory
{
    /** @throws ParserException */
    public function createFromPHPType(mixed $value): BaseToken
    {
        return match (gettype($value)) {
            'string' => new TokenString($value),
            'integer' => new TokenInteger($value),
            'boolean' => TokenBool::fromBool($value),
            'NULL' => new TokenNull($value),
            'double' => new TokenFloat($value),
            'object' => new TokenObject($value),
            'array' => $this->buildTokenCollection($value),
            default => throw ParserException::unsupportedType(gettype($value)),
        };
    }

    public function createFromToken(Token $token, array $matches, int $offset): BaseToken
    {
        $args = [$matches[$token->value], $offset];

        return match ($token) {
            Token::AND => new TokenAnd(...$args),
            Token::OR => new TokenOr(...$args),
            Token::NOT_EQUAL_STRICT => new TokenNotEqualStrict(...$args),
            Token::NOT_EQUAL => new TokenNotEqual(...$args),
            Token::EQUAL_STRICT => new TokenEqualStrict(...$args),
            Token::EQUAL => new TokenEqual(...$args),
            Token::IN => new TokenIn(...$args),
            Token::NOT_IN => new TokenNotIn(...$args),
            Token::BOOL_TRUE => new TokenBoolTrue(...$args),
            Token::BOOL_FALSE => new TokenBoolFalse(...$args),
            Token::NULL => new TokenNull(...$args),
            Token::METHOD => new TokenMethod(...$args),
            Token::FUNCTION => new TokenFunction(...$args),
            Token::VARIABLE => new TokenVariable(...$args),
            Token::FLOAT => new TokenFloat(...$args),
            Token::INTEGER => new TokenInteger(...$args),
            Token::ENCAPSED_STRING => new TokenEncapsedString(...$args),
            Token::SMALLER_EQUAL => new TokenSmallerEqual(...$args),
            Token::GREATER_EQUAL => new TokenGreaterEqual(...$args),
            Token::SMALLER => new TokenSmaller(...$args),
            Token::GREATER => new TokenGreater(...$args),
            Token::OPENING_PARENTHESIS => new TokenOpeningParenthesis(...$args),
            Token::CLOSING_PARENTHESIS => new TokenClosingParenthesis(...$args),
            Token::OPENING_ARRAY => new TokenOpeningArray(...$args),
            Token::CLOSING_ARRAY => new TokenClosingArray(...$args),
            Token::COMMA => new TokenComma(...$args),
            Token::REGEX => new TokenRegex(...$args),
            Token::COMMENT => new TokenComment(...$args),
            Token::NEWLINE => new TokenNewline(...$args),
            Token::SPACE => new TokenSpace(...$args),
            Token::UNKNOWN => new TokenUnknown(...$args),
        };
    }

    /** @throws ParserException */
    private function buildTokenCollection(array $items): TokenArray
    {
        $tokenCollection = new TokenCollection();

        foreach ($items as $item) {
            $tokenCollection->add($this->createFromPHPType($item));
        }

        return new TokenArray($tokenCollection);
    }
}
