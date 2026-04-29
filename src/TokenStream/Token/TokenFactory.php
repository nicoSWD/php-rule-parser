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
            Token::BOOL_TRUE => new TokenBoolTrue(...$args),
            Token::BOOL_FALSE => new TokenBoolFalse(...$args),
            Token::NULL => new TokenNull(...$args),
            Token::FLOAT => new TokenFloat(...$args),
            Token::INTEGER => new TokenInteger(...$args),
            Token::ENCAPSED_STRING => new TokenEncapsedString(...$args),
            Token::REGEX => new TokenRegex(...$args),
            Token::VARIABLE => new TokenVariable(...$args),
            Token::METHOD => new TokenMethod(...$args),
            Token::FUNCTION => new TokenFunction(...$args),
            default => $this->createGeneric($token, ...$args),
        };
    }

    private function createGeneric(Token $token, mixed $value, int $offset): GenericToken
    {
        return new GenericToken(
            $this->tokenToKind($token),
            $value,
            $offset,
        );
    }

    private function tokenToKind(Token $token): TokenKind
    {
        return match ($token) {
            Token::AND => TokenKind::AND,
            Token::OR => TokenKind::OR,
            Token::NOT => TokenKind::NOT,
            Token::NOT_EQUAL_STRICT => TokenKind::NOT_EQUAL_STRICT,
            Token::NOT_EQUAL => TokenKind::NOT_EQUAL,
            Token::EQUAL_STRICT => TokenKind::EQUAL_STRICT,
            Token::EQUAL => TokenKind::EQUAL,
            Token::IN => TokenKind::IN,
            Token::NOT_IN => TokenKind::NOT_IN,
            Token::LESS_THAN_EQUAL => TokenKind::LESS_THAN_EQUAL,
            Token::GREATER_EQUAL => TokenKind::GREATER_EQUAL,
            Token::PLUS => TokenKind::PLUS,
            Token::MINUS => TokenKind::MINUS,
            Token::MULTIPLY => TokenKind::MULTIPLY,
            Token::DIVIDE => TokenKind::DIVIDE,
            Token::MODULO => TokenKind::MODULO,
            Token::LESS_THAN => TokenKind::LESS_THAN,
            Token::GREATER => TokenKind::GREATER,
            Token::OPENING_PARENTHESIS => TokenKind::OPENING_PARENTHESIS,
            Token::CLOSING_PARENTHESIS => TokenKind::CLOSING_PARENTHESIS,
            Token::OPENING_ARRAY => TokenKind::OPENING_ARRAY,
            Token::CLOSING_ARRAY => TokenKind::CLOSING_ARRAY,
            Token::COMMA => TokenKind::COMMA,
            Token::COMMENT => TokenKind::COMMENT,
            Token::NEWLINE => TokenKind::NEWLINE,
            Token::SPACE => TokenKind::SPACE,
            Token::UNKNOWN => TokenKind::UNKNOWN,
            Token::BOOL_TRUE => TokenKind::BOOL_TRUE,
            Token::BOOL_FALSE => TokenKind::BOOL_FALSE,
            Token::NULL => TokenKind::NULL,
            Token::METHOD => TokenKind::METHOD,
            Token::FUNCTION => TokenKind::FUNCTION,
            Token::VARIABLE => TokenKind::VARIABLE,
            Token::FLOAT => TokenKind::FLOAT,
            Token::INTEGER => TokenKind::INTEGER,
            Token::ENCAPSED_STRING => TokenKind::ENCAPSED_STRING,
            Token::REGEX => TokenKind::REGEX,
            default => throw new \RuntimeException('Unexpected token: ' . $token->name),

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
