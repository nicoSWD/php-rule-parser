<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Node;

use Closure;
use nicoSWD\Rule\TokenStream\TokenCollection;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\TokenStream\TokenStream;
use nicoSWD\Rule\TokenStream\Token\TokenType;
use nicoSWD\Rule\TokenStream\Token;

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

    protected function hasMethodCall(): bool
    {
        $stack = $this->tokenStream->getStack();
        $position = $stack->key();
        $hasMethod = false;

        while ($stack->valid()) {
            $stack->next();

            /** @var Token\BaseToken $token */
            if (!$token = $stack->current()) {
                break;
            } elseif ($token->isWhitespace()) {
                continue;
            } elseif ($token->isOfType(TokenType::METHOD)) {
                $this->methodName = $token->getValue();
                $this->methodOffset = $token->getOffset();
                $hasMethod = true;
            } else {
                break;
            }
        }

        $stack->seek($position);

        return $hasMethod;
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

            if ($token->isOfType(TokenType::VALUE | TokenType::INT_VALUE)) {
                if ($commaExpected) {
                    throw ParserException::unexpectedToken($token);
                }

                $commaExpected = true;
                $items->attach($token);
            } elseif ($token->isOfType(TokenType::COMMA)) {
                if (!$commaExpected) {
                    throw new ParserException(sprintf('Unexpected "," at position %d', $token->getOffset()));
                }

                $commaExpected = false;
            } elseif ($token->getType() === $stopAt) {
                break;
            } elseif (!$token->isWhitespace()) {
                throw new ParserException(sprintf(
                    'Unexpected "%s" at position %d',
                    $token->getOriginalValue(),
                    $token->getOffset()
                ));
            }
        } while ($this->tokenStream->valid());

        if (!$commaExpected && $items->count() > 0) {
            throw new ParserException(sprintf('Unexpected "," at position %d', $token->getOffset()));
        }

        $items->rewind();

        return $items;
    }
}
