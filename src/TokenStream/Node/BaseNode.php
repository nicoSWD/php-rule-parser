<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Node;

use Closure;
use nicoSWD\Rule\Grammar\CallableUserFunctionInterface;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\TokenCollection;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\TokenStream;
use nicoSWD\Rule\TokenStream\Token\TokenType;

abstract class BaseNode
{
    protected TokenStream $tokenStream;
    private string $methodName = '';
    private int $methodOffset = 0;

    public function __construct(TokenStream $tokenStream)
    {
        $this->tokenStream = $tokenStream;
    }

    /** @throws ParserException */
    abstract public function getNode(): BaseToken;

    protected function hasMethodCall(): bool
    {
        $stack = $this->tokenStream->getStack();
        $position = $stack->key();
        $hasMethod = false;

        while ($stack->valid()) {
            $stack->next();

            /** @var ?BaseToken $token */
            $token = $stack->current();

            if (!$token) {
                break;
            } elseif ($token->isMethod()) {
                $this->methodName = $token->getValue();
                $this->methodOffset = $stack->key();
                $hasMethod = true;
                break;
            } elseif (!$token->isWhitespace()) {
                break;
            }
        }

        $stack->seek($position);

        return $hasMethod;
    }

    /** @throws ParserException */
    protected function getMethod(BaseToken $token): CallableUserFunctionInterface
    {
        $this->tokenStream->getStack()->seek($this->methodOffset);

        return $this->tokenStream->getMethod($this->getMethodName(), $token);
    }

    private function getMethodName(): string
    {
        return (string) preg_replace('~\W~', '', $this->methodName);
    }

    /** @throws ParserException */
    protected function getFunction(): Closure
    {
        return $this->tokenStream->getFunction($this->getFunctionName());
    }

    private function getFunctionName(): string
    {
        return (string) preg_replace('~\W~', '', $this->getCurrentNode()->getValue());
    }

    /** @throws ParserException */
    protected function getArrayItems(): TokenCollection
    {
        return $this->getCommaSeparatedValues(TokenType::SQUARE_BRACKET);
    }

    /** @throws ParserException */
    protected function getArguments(): TokenCollection
    {
        return $this->getCommaSeparatedValues(TokenType::PARENTHESIS);
    }

    protected function getCurrentNode(): BaseToken
    {
        return $this->tokenStream->getStack()->current();
    }

    /** @throws ParserException */
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

    /** @throws ParserException */
    private function getNextToken(): BaseToken
    {
        $this->tokenStream->next();

        if (!$this->tokenStream->valid()) {
            throw ParserException::unexpectedEndOfString();
        }

        return $this->tokenStream->current();
    }

    /** @throws ParserException */
    private function assertNoTrailingComma(bool $commaExpected, TokenCollection $items, BaseToken $token): void
    {
        if (!$commaExpected && $items->count() > 0) {
            throw ParserException::unexpectedComma($token);
        }
    }
}
