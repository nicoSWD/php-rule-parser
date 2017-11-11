<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Node;

use Closure;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\TokenCollection;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\TokenStream\TokenStream;
use nicoSWD\Rule\TokenStream\Token\TokenType;

abstract class BaseNode
{
    /** @var TokenStream */
    protected $tokenStream;
    /** @var string */
    private $methodName = '';
    /** @var int */
    private $methodOffset = 0;

    public function __construct(TokenStream $tokenStream)
    {
        $this->tokenStream = $tokenStream;
    }

    abstract public function getNode(): BaseToken;

    protected function hasMethodCall(): bool
    {
        $stack = $this->tokenStream->getStack();
        $position = $stack->key();
        $hasMethod = false;

        while ($stack->valid()) {
            $stack->next();

            /** @var BaseToken $token */
            if (!$token = $stack->current()) {
                break;
            } elseif ($token->isWhitespace()) {
                continue;
            } elseif ($token->isMethod()) {
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

    public function getMethod(BaseToken $token): CallableFunction
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
        $items = new TokenCollection();
        $commaExpected = false;

        do {
            $token = $this->getNextToken();

            if ($token->isValue()) {
                if ($commaExpected) {
                    throw ParserException::unexpectedToken($token);
                }

                $commaExpected = true;
                $items->attach($token);
            } elseif ($token->isComma()) {
                if (!$commaExpected) {
                    throw ParserException::unexpectedComma($token);
                }

                $commaExpected = false;
            } elseif ($token->isOfType($stopAt)) {
                break;
            } elseif (!$token->isWhitespace()) {
                throw ParserException::unexpectedToken($token);
            }
        } while (true);

        $this->assertNoTrailingComma($commaExpected, $items, $token);
        $items->rewind();

        return $items;
    }

    private function getNextToken(): BaseToken
    {
        $this->tokenStream->next();

        if (!$this->tokenStream->valid()) {
            throw new ParserException('Unexpected end of string');
        }

        return $this->tokenStream->current();
    }

    private function assertNoTrailingComma(bool $commaExpected, TokenCollection $items, BaseToken $token)
    {
        if (!$commaExpected && $items->count() > 0) {
            throw ParserException::unexpectedComma($token);
        }
    }
}
