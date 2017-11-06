<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Token;

use nicoSWD\Rule\Tokenizer\TokenStack;
use nicoSWD\Rule\TokenStream\TokenStream;

abstract class BaseToken
{
    /** @var mixed */
    protected $value;
    /** @var int */
    protected $offset = 0;
    /** @var TokenStack */
    protected $stack;
    /** @var int */
    protected $position = null;
    /** @var int */
    protected $line = null;

    abstract public function getType(): int;

    public function __construct($value, int $offset = 0, TokenStack $stack = null)
    {
        $this->value = $value;
        $this->offset = $offset;
        $this->stack = $stack;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Some tokens can be represented by different operators, so the original value is used for error reporting,
     * while the other one is used internally.
     *
     * @return mixed
     */
    final public function getOriginalValue()
    {
        return $this->value;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getStack(): TokenStack
    {
        return $this->stack;
    }

    public function setStack(TokenStack $stack)
    {
        $this->stack = $stack;
    }

    public function isWhitespace(): bool
    {
        return false;
    }

    public function isOfType(int $type): bool
    {
        return $this->getType() === $type;
    }

    public function createNode(TokenStream $tokenStream): self
    {
        return $this;
    }

    public function getPosition(): int
    {
        if (!isset($this->position)) {
            $this->getLineAndPosition();
        }

        return $this->position;
    }

    public function getLine(): int
    {
        if (!isset($this->line)) {
            $this->getLineAndPosition();
        }

        return $this->line;
    }

    private function getLineAndPosition()
    {
        $this->line = 1;
        $this->position = 0;

        foreach ($this->stack as $token) {
            $sumPosition = true;

            if ($token === $this) {
                break;
            } elseif ($token instanceof TokenNewline) {
                $this->line += 1;
                $this->position = 0;
                $sumPosition = false;
            } elseif ($token instanceof TokenComment) {
                $this->line += substr_count($token->getValue(), "\n");
            }

            if ($sumPosition) {
                $this->position += strlen($token->getOriginalValue());
            }
        }
    }
}
