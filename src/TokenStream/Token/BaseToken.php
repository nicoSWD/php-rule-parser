<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Token;

use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\TokenStream;

abstract class BaseToken
{
    abstract public function getType(): int;

    public function __construct(
        private mixed $value,
        private int $offset = 0
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

    /** @throws ParserException */
    public function createNode(TokenStream $tokenStream): self
    {
        return $this;
    }

    public function isOfType(int $type): bool
    {
        return ($this->getType() | $type) === $type;
    }

    public function isValue(): bool
    {
        return $this->isOfType(TokenType::VALUE | TokenType::INT_VALUE);
    }

    public function isWhitespace(): bool
    {
        return $this->isOfType(TokenType::SPACE | TokenType::COMMENT);
    }

    public function isMethod(): bool
    {
        return $this->isOfType(TokenType::METHOD);
    }

    public function isComma(): bool
    {
        return $this->isOfType(TokenType::COMMA);
    }

    public function isOperator(): bool
    {
        return $this->isOfType(TokenType::OPERATOR);
    }

    public function isLogical(): bool
    {
        return $this->isOfType(TokenType::LOGICAL);
    }

    public function isParenthesis(): bool
    {
        return $this->isOfType(TokenType::PARENTHESIS);
    }
}
