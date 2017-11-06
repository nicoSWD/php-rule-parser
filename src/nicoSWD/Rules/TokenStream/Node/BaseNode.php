<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\TokenStream\Node;

use Closure;
use nicoSWD\Rules\TokenStream\TokenCollection;
use nicoSWD\Rules\Parser\Exception\ParserException;
use nicoSWD\Rules\Grammar\CallableFunction;
use nicoSWD\Rules\TokenStream\TokenStream;
use nicoSWD\Rules\TokenStream\Token\TokenType;
use nicoSWD\Rules\TokenStream\Token;

abstract class BaseNode
{
    /** @var TokenStream */
    protected $tokenStream;
    /** @var string */
    protected $methodName = '';
    /** @var int */
    protected $methodOffset = 0;

    public function __construct(TokenStream $tokenStream)
    {
        $this->tokenStream = $tokenStream;
    }

    abstract public function getNode(): Token\BaseToken;

    /**
     * Looks ahead, but does not move the pointer.
     */
    protected function hasMethodCall(): bool
    {
        $stackClone = $this->tokenStream->getStack()->getClone();

        while ($stackClone->valid()) {
            $stackClone->next();

            if (!$token = $stackClone->current()) {
                break;
            } elseif ($token->isWhitespace()) {
                continue;
            } elseif ($token->isOfType(TokenType::METHOD)) {
                $this->methodName = $token->getValue();
                $this->methodOffset = $token->getOffset();

                return true;
            } else {
                break;
            }
        }

        return false;
    }

    public function getMethod(Token\BaseToken $token): CallableFunction
    {
        return $this->tokenStream->getMethod($this->getMethodName(), $token);
    }

    private function getMethodName(): string
    {
        do {
            $this->tokenStream->next();
        } while ($this->getCurrentNode()->getOffset() < $this->methodOffset);

        return trim(ltrim(rtrim($this->methodName, "\r\n("), '.'));
    }

    public function getFunction(): Closure
    {
        return $this->tokenStream->getFunction($this->getFunctionName());
    }

    private function getFunctionName(): string
    {
        return rtrim($this->getCurrentNode()->getValue(), " \r\n(");
    }

    public function getArrayItems(): TokenCollection
    {
        return $this->getCommaSeparatedValues(TokenType::SQUARE_BRACKET);
    }

    public function getArguments(): TokenCollection
    {
        return $this->getCommaSeparatedValues(TokenType::PARENTHESIS);
    }

    public function getCurrentNode()
    {
        return $this->tokenStream->getStack()->current();
    }

    private function getCommaSeparatedValues(int $stopAt): TokenCollection
    {
        $commaExpected = false;
        $items = new TokenCollection();

        do {
            $this->tokenStream->next();

            if (!$token = $this->tokenStream->current()) {
                throw new ParserException('Unexpected end of string');
            }

            if ($token->getType() & (TokenType::VALUE | TokenType::INT_VALUE)) {
                if ($commaExpected) {
                    throw ParserException::unexpectedToken($token);
                }

                $commaExpected = true;
                $items->attach($token);
            } elseif ($token->isOfType(TokenType::COMMA)) {
                if (!$commaExpected) {
                    throw ParserException::unexpectedToken($token);
                }

                $commaExpected = false;
            } elseif ($token->getType() === $stopAt) {
                break;
            } elseif (!$token->isWhitespace()) {
                throw ParserException::unexpectedToken($token);
            }
        } while ($this->tokenStream->valid());

        if (!$commaExpected && $items->count() > 0) {
            throw new ParserException(sprintf(
                'Unexpected "," at position %d on line %d',
                $token->getPosition(),
                $token->getLine()
            ));
        }

        $items->rewind();

        return $items;
    }
}
