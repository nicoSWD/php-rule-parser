<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\TokenStream\Token;

abstract class BaseToken
{
    abstract public function getKind(): TokenKind;

    public function __construct(
        private readonly mixed $value,
        private readonly int $offset = 0,
    ) {
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    final public function getOriginalValue(): mixed
    {
        return $this->value;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getType(): TokenType
    {
        return match ($this->getKind()) {
            TokenKind::AND, TokenKind::OR => TokenType::LOGICAL,
            TokenKind::NOT, TokenKind::NOT_EQUAL, TokenKind::NOT_EQUAL_STRICT,
            TokenKind::EQUAL, TokenKind::EQUAL_STRICT, TokenKind::IN, TokenKind::NOT_IN,
            TokenKind::LESS_THAN, TokenKind::LESS_THAN_EQUAL,
            TokenKind::GREATER, TokenKind::GREATER_EQUAL,
            TokenKind::PLUS, TokenKind::MINUS,
            TokenKind::MULTIPLY, TokenKind::DIVIDE, TokenKind::MODULO => TokenType::OPERATOR,
            TokenKind::OPENING_PARENTHESIS, TokenKind::CLOSING_PARENTHESIS => TokenType::PARENTHESIS,
            TokenKind::OPENING_ARRAY, TokenKind::CLOSING_ARRAY => TokenType::SQUARE_BRACKET,
            TokenKind::COMMA => TokenType::COMMA,
            TokenKind::COMMENT => TokenType::COMMENT,
            TokenKind::NEWLINE, TokenKind::SPACE => TokenType::SPACE,
            TokenKind::UNKNOWN => TokenType::UNKNOWN,
            TokenKind::METHOD => TokenType::METHOD,
            TokenKind::FUNCTION => TokenType::FUNCTION,
            TokenKind::VARIABLE => TokenType::VARIABLE,
            TokenKind::STRING, TokenKind::INTEGER, TokenKind::FLOAT,
            TokenKind::BOOL_TRUE, TokenKind::BOOL_FALSE, TokenKind::NULL,
            TokenKind::REGEX, TokenKind::OBJECT, TokenKind::ARRAY => TokenType::VALUE,
            TokenKind::ENCAPSED_STRING => TokenType::VALUE,
        };
    }

    public function isOfKind(TokenKind $kind): bool
    {
        return $this->getKind() === $kind;
    }

    public function canBeIgnored(): bool
    {
        $kind = $this->getKind();

        return $kind === TokenKind::SPACE
            || $kind === TokenKind::NEWLINE
            || $kind === TokenKind::COMMENT;
    }
}
