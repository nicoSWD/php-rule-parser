<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Token;

use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\TokenIterator;

abstract class BaseToken
{
    abstract public function getType(): TokenType;

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

    /** @throws ParserException */
    public function createNode(TokenIterator $tokenStream): self
    {
        return $this;
    }

    public function isOfType(TokenType $type): bool
    {
        return $this->getType() === $type;
    }

    public function canBeIgnored(): bool
    {
        return
            $this->isOfType(TokenType::SPACE) ||
            $this->isOfType(TokenType::COMMENT);
    }
}
