<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
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
            default => throw ParserException::unsupportedType(gettype($value))
        };
    }

    /** @throws ParserException */
    public function createFromTokenName(string $tokenName): string
    {
        return match ($tokenName) {
            Token::AND => TokenAnd::class,
            Token::OR => TokenOr::class,
            Token::NOT_EQUAL_STRICT => TokenNotEqualStrict::class,
            Token::NOT_EQUAL => TokenNotEqual::class,
            Token::EQUAL_STRICT => TokenEqualStrict::class,
            Token::EQUAL => TokenEqual::class,
            Token::IN => TokenIn::class,
            Token::NOT_IN => TokenNotIn::class,
            Token::BOOL_TRUE => TokenBoolTrue::class,
            Token::BOOL_FALSE => TokenBoolFalse::class,
            Token::NULL => TokenNull::class,
            Token::METHOD => TokenMethod::class,
            Token::FUNCTION => TokenFunction::class,
            Token::VARIABLE => TokenVariable::class,
            Token::FLOAT => TokenFloat::class,
            Token::INTEGER => TokenInteger::class,
            Token::ENCAPSED_STRING => TokenEncapsedString::class,
            Token::SMALLER_EQUAL => TokenSmallerEqual::class,
            Token::GREATER_EQUAL => TokenGreaterEqual::class,
            Token::SMALLER => TokenSmaller::class,
            Token::GREATER => TokenGreater::class,
            Token::OPENING_PARENTHESIS => TokenOpeningParenthesis::class,
            Token::CLOSING_PARENTHESIS => TokenClosingParenthesis::class,
            Token::OPENING_ARRAY => TokenOpeningArray::class,
            Token::CLOSING_ARRAY => TokenClosingArray::class,
            Token::COMMA => TokenComma::class,
            Token::REGEX => TokenRegex::class,
            Token::COMMENT => TokenComment::class,
            Token::NEWLINE => TokenNewline::class,
            Token::SPACE => TokenSpace::class,
            Token::UNKNOWN => TokenUnknown::class,
            default => throw ParserException::unknownTokenName($tokenName)
        };
    }

    /** @throws ParserException */
    private function buildTokenCollection(array $items): TokenArray
    {
        $tokenCollection = new TokenCollection();

        foreach ($items as $item) {
            $tokenCollection->attach($this->createFromPHPType($item));
        }

        return new TokenArray($tokenCollection);
    }
}
